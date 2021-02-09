<?php

namespace App\Services;

use App\Models\Aircraft;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;

class MultipleBookingService
{
    private $letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    private $numColumns;

    private $matrix = [];

    /** @var Flight */
    private $flight;

    /** @var Aircraft */
    private $aircraft;

    /** @var User */
    private $user;

    private function getSeatName(int $row, int $column): string
    {
        return "{$this->letters[$column]}{$row}";
    }

    private function getReservedSeatsNames(): array
    {
        return $this->flight->bookings()
            ->join('seats', 'seats.id', '=', 'bookings.seat_id')
            ->select('seats.name')->get()->pluck('name')->toArray();
    }

    private function mountMatrix()
    {
        $this->numColumns = substr_count($this->aircraft->row_arrangement, " ");
        $reservedSeatsNames = $this->getReservedSeatsNames();
        $this->matrix = [];
        for ($i = 0; $i < $this->aircraft->rows; $i++)
        {
            for ($j = 0; $j < $this->numColumns; $j++)
            {
                $seatName = $this->getSeatName($i, $j);
                $matrix[$i][$j] = in_array($seatName, $reservedSeatsNames) ? 'X' : $seatName;
            }
        }
    }

    private function bookOnLeftRow(int $row, int $mid, int $numSeats): ?array
    {
        $reserved = 0;
        $startColumn = 0;

        for (
            $j = 0, $remaining = $mid;
            $j < $mid && $remaining >= $numSeats && $reserved < $numSeats;
            $j++, $remaining--
        )
        {
            if ($this->matrix[$row][$j] == 'X')
            {
                $reserved = 0;
                $startColumn = $j + 1;
            }
            else $reserved++;
        }

        return $reserved == $numSeats ? [$startColumn, $startColumn + $numSeats - 1] : null;
    }

    private function bookOnRightRow(int $row, int $mid, int $numSeats): ?array
    {
        $reserved = 0;
        $endColumn = $this->numColumns - 1;

        for (
            $j = $this->numColumns - 1, $remaining = $this->numColumns - $mid;
            $j >= $mid && $remaining >= $numSeats && $reserved < $numSeats;
            $j--, $remaining--
        )
        {
            if ($this->matrix[$row][$j] == 'X')
            {
                $reserved = 0;
                $endColumn = $j - 1;
            }
            else $reserved++;
        }

        return $reserved == $numSeats ? [$endColumn - $numSeats + 1, $endColumn] : null;
    }

    private function getSeatsNames(int $row, array $startAndEndColumn): array
    {
        $seatsNames = [];
        $start = $startAndEndColumn[0];
        $end = $startAndEndColumn[1];

        for ($i = $start; $i <= $end; $i++)
        {
            $seatsNames = $this->getSeatName($row, $i);
        }

        return $seatsNames;
    }

    private function tryToBookOnSameRowWithoutCrossingTheAisle(int $numSeats): ?array
    {
        $mid = intdiv($this->numColumns, 2);

        if ($numSeats > $mid) return null;

        $startAndEndColumn = null;
        $i = 0;

        for (; $i < $this->aircraft->rows && !$startAndEndColumn; $i++)
        {
            $startAndEndColumn = $this->bookOnLeftRow($i, $mid, $numSeats);
            if ($startAndEndColumn == null)
                $startAndEndColumn = $this->bookOnRightRow($i, $mid, $numSeats);
        }

        return $this->getSeatsNames($i, $startAndEndColumn);
    }

    private function bookSeats($seatsNames): array
    {
        $seatsIds = $this->flight->bookings()
            ->join('seats', 'seats.id', '=', 'bookings.seat_id')
            ->whereIn('seats.name', $seatsNames)
            ->select('seats.id')->get()->pluck('id')->toArray();

        return array_map(function ($seatId) {
            return Booking::create([
                'user_id' => $this->user->id,
                'flight_id' => $this->flight->id,
                'seat_id' => $seatId,
            ]);
        }, $seatsIds);
    }

    private function tryToBookRectangleOnLeftSide(
        int $startLine, int $startColumn, int $numLines, int $numColumns,
        int $numSeats, array &$seatsNames
    ): ?array
    {
        $seatsNames = [];

        for ($i = $startLine; $i < $startLine + $numLines; $i++)
            for ($j = $startColumn; $j < $startColumn + $numColumns; $j++)
                if ($this->matrix[$i][$j] == 'X') return [$i, $j];
                else
                {
                    array_push($seatsNames, $this->getSeatName($i, $j));
                    $numSeats--;
                    if ($numSeats == 0) return null;
                }

        return null;
    }

    private function tryToBalanceAcrossRowsOnLeftSide(array &$seatsNames, int $mid, int $numSeats): bool
    {
        $numOfNecessaryRows = intdiv($numSeats, $mid);
        $numColumns = $numSeats;
        if ($numSeats == 4) $numColumns = 2;
        else if ($numSeats > 4) $numColumns = 3;

        $i = 0;
        $stopPoint = [];
        while ($i + $numOfNecessaryRows <= $this->aircraft->rows && $stopPoint != null)
        {
            $stopPoint = $this->tryToBookRectangleOnLeftSide(
                $i, 0, $numOfNecessaryRows, $numColumns, $numSeats, $seatsNames
            );
            if ($stopPoint != null) $i = $stopPoint[0] + 1;
        }

        if ($stopPoint != null) $seatsNames = [];
        return $stopPoint == null;
    }

    private function tryToBookRectangleOnRightSide(
        int $startLine, int $startColumn, int $numLines, int $numColumns,
        int $numSeats, array &$seatsNames
    ): ?array
    {
        $seatsNames = [];

        for ($i = $startLine; $i > $startLine - $numLines; $i--)
            for ($j = $startColumn; $j > $startColumn - $numColumns; $j--)
                if ($this->matrix[$i][$j] == 'X') return [$i, $j];
                else
                {
                    array_push($seatsNames, $this->getSeatName($i, $j));
                    $numSeats--;
                    if ($numSeats == 0) return null;
                }

        return null;
    }

    private function tryToBalanceAcrossRowsOnRightSide(array &$seatsNames, int $mid, int $numSeats): bool
    {
        $numOfNecessaryRows = intdiv($numSeats, $mid);
        $numColumns = $numSeats;
        if ($numSeats == 4) $numColumns = 2;
        else if ($numSeats > 4) $numColumns = 3;

        $i = 0;
        $stopPoint = [];
        while ($i + $numOfNecessaryRows <= $this->aircraft->rows && $stopPoint != null)
        {
            $stopPoint = $this->tryToBookRectangleOnRightSide(
                $i, $this->numColumns, $numOfNecessaryRows, $numColumns, $numSeats, $seatsNames
            );
            if ($stopPoint != null) $i = $stopPoint[0] + 1;
        }

        if ($stopPoint != null) $seatsNames = [];
        return $stopPoint == null;
    }

    private function tryToBalanceAcrossRows(int $numSeats): ?array
    {
        $mid = intdiv($this->numColumns, 2);
        $seatsNames = [];
        if ($this->tryToBalanceAcrossRowsOnLeftSide($seatsNames, $mid, $numSeats))
            return $seatsNames;
        if ($this->tryToBalanceAcrossRowsOnRightSide($seatsNames, $mid, $numSeats))
            return $seatsNames;
        return null;
    }

    private function tryToBookNearbyAcrossTheAisle(int $numSeats): ?array
    {
        $mid = intdiv($this->numColumns, 2);
        $numOfNecessaryRows = ceil($numSeats / 2);
        $numColumns = 2;

        $i = 0;
        $stopPoint = [];
        $seatsNames = [];
        while ($i + $numOfNecessaryRows <= $this->aircraft->rows && $stopPoint != null)
        {
            $stopPoint = $this->tryToBookRectangleOnLeftSide(
                $i, $mid - 1, $numOfNecessaryRows, $numColumns, $numSeats, $seatsNames
            );
            if ($stopPoint != null) $i = $stopPoint[0] + 1;
        }

        return $stopPoint == null ? $seatsNames : null;
    }

    private function tryToBookRandomly(int $numSeats): ?array
    {
        $reserved = 0;
        $seatsNames = [];

        for ($i = $this->aircraft->rows - 1; $i >= 0; $i--)
            for ($j = 0; $j < $this->numColumns; $j++)
                if ($this->matrix[$i][$j] != 'X')
                {
                    array_push($seatsNames, $this->getSeatName($i, $j));
                    $reserved++;
                    if ($reserved == $numSeats) return $seatsNames;
                }

        return null;
    }

    public function book(array $params)
    {
        $data = \Validator::make($params, [
            'flight_id' => 'required|string',
            'user_id' => 'required|numeric|min:1',
            'num_seats' => 'required|numeric|min:1',
        ])->validate();

        $this->flight = Flight::find($data['flight_id']);
        $this->aircraft = $this->flight->aircraft;
        $this->user = User::find($data['user_id']);
        $numSeats = $data['num_seats'];
        $this->mountMatrix();

        $seats = $this->tryToBookOnSameRowWithoutCrossingTheAisle($numSeats);
        if ($seats != null) return $this->bookSeats($seats);

        $seats = $this->tryToBalanceAcrossRows($numSeats);
        if ($seats != null) return $this->bookSeats($seats);

        $seats = $this->tryToBookNearbyAcrossTheAisle($numSeats);
        if ($seats != null) return $this->bookSeats($seats);

        $seats = $this->tryToBookRandomly($numSeats);
        if ($seats != null) return $this->bookSeats($seats);

        throw new \Exception("Could not book {$numSeats} seat(s) for this flight");
    }
}
