<?php

namespace App\Http\Controllers;

use App\Models\Poker;
use Illuminate\Http\Request;

class PokerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pokers = $this->getAllPlayers();
        return response()->json($pokers);
    }

    public function getAllPlayers()
    {
        $pokers = Poker::orderBy('id', 'desc')->get(['id', 'name', 'code', 'amount', 'agent', 'blacklist', 'isTime', 'playtime']);
        return $pokers;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Poker::updateOrCreate(
            ['code' => $request->code],
            $request->post()
        );
        $data = $this->getAllPlayers();

        return response()->json([
            'message' => 'Player Created Successfully!!',
            'pokers' => $data
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\poker  $poker
     * @return \Illuminate\Http\Response
     */
    public function show(Poker $poker)
    {
        return response()->json($poker);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\poker  $poker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Poker $poker)
    {
        $poker->fill($request->post())->save();

        return response()->json([
            'message' => 'Player Updated Successfully!!',
            'poker' => $poker
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\poker  $poker
     * @return \Illuminate\Http\Response
     */
    public function destroy(Poker $poker)
    {
        $poker->delete();

        return response()->json([
            'message' => 'Category Deleted Successfully!!'
        ]);
    }

    public function blacklist(Request $request, Poker $poker)
    {
        $requestIDs = collect($request->all())->pluck('id');

        collect($request->all())->each(function ($item) {
            Poker::where('id', $item['id'])->update(['blacklist' => 1]);
        });

        Poker::whereNotIn('id', $requestIDs)->update(['blacklist' => 0]);

        return response()->json([
            'message' => 'Blacklist Updated Successfully!!',
            'data' => $request->post(),
        ]);
    }
    public function timer(Request $request, Poker $poker)
    {
        Poker::where('id', $request->id)->update(['playtime' => $request->playtime]);

        return true;
    }
    public function isTime(Request $request, Poker $poker)
    {
        Poker::where('id', $request->id)->update(['isTime' => $request->isTime]);

        return true;
    }
    public function resetAllTime(Request $request, Poker $poker)
    {
        Poker::query()->update(['playtime' => '0', 'isTime' => '0']);

        $pokers = $this->getAllPlayers();

        return response()->json([
            'message' => 'All playtime reseted Successfully!!',
            'pokers' => $pokers,
        ]);
    }
    public function stopAllTimer(Request $request, Poker $poker)
    {
        Poker::query()->update(['isTime' => '0']);

        $pokers = $this->getAllPlayers();

        return response()->json([
            'message' => 'All playtime reseted Successfully!!',
            'pokers' => $pokers,
        ]);
    }
    public function createPlayers(Request $request, Poker $poker)
    {
        $maxDate = Poker::max('date');
        $players = Poker::where('date', $maxDate)->get();

        foreach ($players as $player) {
            $newPlayer = $player->replicate();
            $newPlayer->date = $request->date;
            $newPlayer->blacklist = 0;
            $newPlayer->isTime = 0;
            $newPlayer->playtime = 0;
            $newPlayer->amount = 0;
            $newPlayer->save();
        }
        $pokers = $this->getAllPlayers();

        return response()->json([
            'message' => 'All playtime reseted Successfully!!',
            'pokers' => $pokers,
        ]);
    }
}