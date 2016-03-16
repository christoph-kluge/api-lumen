<?php namespace App\Console\Commands;

/**
 * @package ReactServeCommand.php
 * @author  Christoph Kluge <work@christoph-kluge.eu>
 * @since   08.03.16
 */
use App\Server;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ReactPhpServeCommand extends Command
{

  /**
   * The console command name.
   * @var string
   */
  protected $name = 'api:serve';

  /**
   * The console command description.
   * @var string
   */
  protected $description = 'Start dinog-api running through reactphp';

  /**
   * Execute the console command.
   * @return void
   */
  public function fire()
  {
    $host = $this->input->getOption('host');
    $port = $this->input->getOption('port');
    $this->info("Laravel ReactPHP server started on http://{$host}:{$port}");
    with(new Server($this->getLaravel(), $host, $port))->run();
  }

  /**
   * Get the console command options.
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on.', 'localhost'],
      ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on.', 8001],
    ];
  }

}