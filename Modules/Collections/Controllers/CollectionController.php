<?php

namespace Modules\Collections\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Laravel\Jetstream\RedirectsActions;
use Modules\Collections\Contracts\CreatesCollection;
use Modules\Collections\Contracts\DeletesCollection;
use Modules\Collections\Contracts\UpdatesCollection;
use Modules\Collections\Models\Collection;

class CollectionController extends Controller
{
    use RedirectsActions;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        Gate::forUser($request->user())->authorize('create', Collection::class);

        return Inertia::render('Collections::Create', $request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $creator = app(CreatesCollection::class);

        $creator->create($request->user(), $request->all());

        return $this->redirectPath($creator);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Collection $collection)
    {
        Gate::forUser($request->user())->authorize('view', $collection);

        return Inertia::render('Collections::Show', [
            'collection' => $collection,
            'permissions' => [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collection $collection)
    {
        $updater = app(UpdatesCollection::class);

        $updater->update($request->user(), $collection, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Collection $collection)
    {
        $deleter = app(DeletesCollection::class);

        $deleter->delete($request->user(), $collection);

        return $this->redirectPath($deleter);
    }
}
