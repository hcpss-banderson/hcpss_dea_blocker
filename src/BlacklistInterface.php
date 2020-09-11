<?php

namespace Drupal\hcpss_dea_blocker;

interface BlacklistInterface {
  
  /**
   * Check to see if a domain is blacklisted.
   * 
   * @param string $domain
   * @return bool
   */
  public function isBlacklisted(string $domain): bool;
  
  /**
   * Check to see if an email address is blacklisted.
   * 
   * @param string $email
   * @return bool
   */
  public function isEmailBlacklisted(string $email): bool;
}
