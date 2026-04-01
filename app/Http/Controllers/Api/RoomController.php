<?php

namespace App\Http\Controllers\Api;

use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;
use App\Http\Controllers\Controller;

class RoomController extends Controller
{
    private RoomRepositoryInterface $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function index()
    {
        $rooms = $this->roomRepository->get(['floor']);

        if ($rooms->isEmpty()) {
            return response()->json([
                'message' => 'No rooms available.',
                'data'    => [],
            ]);
        }

        return response()->json([
            'message' => 'Rooms fetched successfully.',
            'data'    => $rooms->map(fn($room) => [
                'id'       => $room->id,
                'number'   => $room->number,
                'capacity' => $room->capacity,
                'price'    => $room->price / 100,
                'floor'    => [
                    'id'   => $room->floor->id,
                    'name' => $room->floor->name,
                ],
            ]),
        ]);
    }
}
