<?php

namespace Drupal\hcpss_dea_blocker;

use Drupal\Core\State\StateInterface;

class BlacklistService implements BlacklistInterface {
  
  /**
   * @var StateInterface
   */
  private $state;
  
  /**
   * The remote location of the json encoded blacklist.
   * 
   * @var string
   */
  private $blacklistUri = 'https://raw.githubusercontent.com/ivolo/disposable-email-domains/master/index.json';
  
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }
  
  /**
   * {@inheritDoc}
   * @see BlacklistInterface::isBlacklisted()
   */
  public function isBlacklisted(string $domain): bool {
    $blacklist = $this->state->get('hcpss_dea_blocker.blacklist', []);
    
    return in_array($domain, $blacklist);
  }
  
  /**
   * {@inheritDoc}
   * @see BlacklistInterface::isEmailBlacklisted()
   */
  public function isEmailBlacklisted(string $email): bool {
    $parts = explode('@', $email);
    $domain = array_pop($parts);
    
    return $this->isBlacklisted($domain);
  }
  
  /**
   * Refresh the blacklist data.
   */
  public function refresh() {
    $payload = file_get_contents($this->blacklistUri);
    $blacklist = json_decode($payload, TRUE);
    $this->state->set('hcpss_dea_blocker.blacklist', $blacklist);
    $this->state->set('hcpss_dea_blocker.last_sync', REQUEST_TIME);
  }
  
  /**
   * Runs with cron.
   */
  public function cron() {
    $last_sync = $this->state->get('hcpss_dea_blocker.last_sync', 0);
    
    if (($last_sync + 3600 * 72) < REQUEST_TIME) {
      $this->refresh();
    }
  }
}
