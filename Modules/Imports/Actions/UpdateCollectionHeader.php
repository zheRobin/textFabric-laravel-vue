<?php

namespace Modules\Imports\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Collections\Models\Collection;
use Modules\Imports\Contracts\UpdatesCollectionHeader;
use Modules\Imports\Enums\HeaderTypeEnum;

class UpdateCollectionHeader implements UpdatesCollectionHeader
{
    public function update(User $user, Collection $collection, array $input): void
    {
        Gate::forUser($user)->authorize('update', $collection);

        Validator::make($input, [
            'header' => ['required', 'string'],
            'type' => ['required', Rule::in(HeaderTypeEnum::values())]
        ])->validateWithBag('updateCollectionHeader');

       $headers = [];

       foreach ($collection->headers as $header) {
           if($input['type'] === 'title'){
               if ($header['name'] === $input['header']) {
                   $header['type'] = $input['type'];
               }else{
                   $header['type'] = 'text';
               }
           }else{
               if ($header['name'] === $input['header']) {
                   $header['type'] = $input['type'];
               }
           }

           $headers[] = $header;
       }

       $collection->update(['headers' => $headers]);
    }
}
