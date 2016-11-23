<?php
namespace Affilicious\Product\Application\Update\Manager;

use Affilicious\Product\Application\Update\Queue\Update_Mediator_Interface;
use Affilicious\Product\Application\Update\Worker\Update_Worker_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Manager_Interface
{
    /**
     * @since 0.7
     * @param Update_Mediator_Interface $mediator
     */
    public function __construct(Update_Mediator_Interface $mediator);

    /**
     * Check by name if the worker exists in the manager.
     *
     * @since 0.7
     * @param string $name
     * @return bool
     */
    public function has_worker($name);

    /**
     * Add a new update worker.
     *
     * @since 0.7
     * @param Update_Worker_Interface $worker
     */
    public function add_worker(Update_Worker_Interface $worker);

    /**
     * Remove an existing update worker by the name.
     *
     * @since 0.7
     * @param string $name
     */
    public function remove_worker($name);

    /**
     * Get all update workers.
     *
     * @since 0.7
     * @return Update_Worker_Interface[]
     */
    public function get_workers();

    /**
     * Run the tasks for the given update interval like hourly, twice daily or daily.
     *
     * @since 0.7
     * @param string $update_interval
     */
    public function run_tasks($update_interval);
}
