<?php

namespace Modules\Export\Jobs;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\OpenAI\Contracts\BuildsParams;
use OpenAI\Laravel\Facades\OpenAI;

class ProccesExportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $pres;
    protected $item;
    protected $export;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $pres, $item, $export)
    {
        $this->user = $user;
        $this->pres = $pres;
        $this->item = $item;
        $this->export = $export;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $builder = app(BuildsParams::class);
        $params = $builder->build($this->user, $this->pres, $this->item);
        $response = OpenAI::chat()->create($params);
        $content = $response->choices[0]->message->content;
        $data[$this->pres->id][$this->item->id] = $content;
        $this->export->data = [...$this->export->data, $data];
        $this->export->save();
    }
}
