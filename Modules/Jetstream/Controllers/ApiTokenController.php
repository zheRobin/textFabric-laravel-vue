<?php

namespace Modules\Jetstream\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;
use Modules\RestApi\Requests\GenerateRequest;

class ApiTokenController extends Controller
{
    /**
     * Show the user API token screen.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        return Jetstream::inertia()->render($request, 'API/Index', [
            'tokens' => $request->user()->currentTeam->tokens->map(function ($token) {
                return $token->toArray() + [
                        'last_used_ago' => optional($token->last_used_at)->diffForHumans(),
                    ];
            }),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
            'apiDocumentations' => (new GenerateRequest)->rules()
        ]);
    }

    /**
     * Create a new API token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('personal_access_tokens', 'name')->where(function ($query) use ($request) {
                return $query->where('tokenable_id', $request->user()->id);
            })],
        ]);

        $token = $request->user()->currentTeam->createToken(
            $request->name,
            Jetstream::validPermissions($request->input('permissions', []))
        );

        return back()->with('flash', [
            'token' => explode('|', $token->plainTextToken, 2)[1],
        ]);
    }


    /**
     * Update the given API token's permissions.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $tokenId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $tokenId)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        $token = $request->user()->currentTeam->tokens()->where('id', $tokenId)->firstOrFail();

        $token->forceFill([
            'abilities' => Jetstream::validPermissions($request->input('permissions', [])),
        ])->save();

        return back(303);
    }

    /**
     * Delete the given API token.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $tokenId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $tokenId)
    {
        $request->user()->currentTeam->tokens()->where('id', $tokenId)->first()->delete();

        return back(303);
    }
}
