<?php

namespace Modules\OpenAI\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\OpenAI\Actions\BuildDemoParams;
use Modules\OpenAI\Actions\CompleteDemoItemStreamed;

class DemoItemCompletionController extends Controller
{
    public function __invoke(Request $request)
    {
        $requestData = json_decode($request->item, true);

        $builder = app(BuildDemoParams::class);

        $config = $builder->build($requestData, strval($request->system_prompt), strval($request->user_prompt));

        return response()->stream(function () use ($config) {
            $completer = app(CompleteDemoItemStreamed::class);

            $completer->complete($config);
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
            'Content-Encoding' => 'none',
        ]);
    }
}
