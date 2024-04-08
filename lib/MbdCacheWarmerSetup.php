<?php
class MbdCacheWarmerSetup
{
    public string $cache_warmer_hook_name = 'mbd_cache_warmer';
    public function install()
    {
        add_action('init', array($this, 'executeMbdCacheWarmerCron'));
        add_action($this->cache_warmer_hook_name, array($this, 'executeMbdCacheWarmerFunctionality'));
    }

    public function executeMbdCacheWarmerCron(): void
    {
        if ( ! wp_next_scheduled( 'mbd_cache_warmer' ) ) {
            wp_schedule_event(time(), 'twicedaily', $this->cache_warmer_hook_name);
        }
    }

    public function executeMbdCacheWarmerFunctionality(): void
    {
        require __DIR__ . '/MbdCacheWarmerHelper.php';

        $helper = new MbdCacheWarmerHelper();
        $helper->startWarmer();
    }
}
