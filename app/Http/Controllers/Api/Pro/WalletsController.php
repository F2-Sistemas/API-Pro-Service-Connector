<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class WalletsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        $perPage = $request->input('per_page') ?: $request->input('perPage');
        $perPage = filter_var($perPage, FILTER_VALIDATE_INT) && $perPage > 0 ? intval($perPage) : 20;
        $mainOnly = $request->boolean('mainOnly');

        $query = Wallet::query()
            ->where('user_id', $user?->id)
            ->when($mainOnly, fn (Builder $q) => $q->where('main', true));

        return response()->json(
            collect($query->paginate($perPage))->merge([
                'filters' => array_filter([
                    'perPage' => $perPage,
                    'mainOnly' => $mainOnly,
                ], fn ($item) => !is_null($item)),
            ])
        );
    }

    public function wallet(Request $request, int|string $walletUuid)
    {
        $user = Auth::user();

        abort_if(!$user, 403);

        $wallet = Wallet::query()
            ->where('user_id', $user?->id)
            ->where('uuid', $walletUuid)
            ->first();

        abort_if(!$wallet, 404);

        return response()->json($wallet);
    }
}
