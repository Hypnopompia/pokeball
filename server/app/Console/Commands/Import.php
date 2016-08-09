<?php

namespace App\Console\Commands;

use App\Pokeball;
use App\Pokemon;
use App\Sighting;
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

class Import extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'command:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$pokeball = Pokeball::find(1);
		if (!$pokeball) {
			echo "Pokeball not found.\n";
			return;
		}

		$pokeball->scan();

	}
}
