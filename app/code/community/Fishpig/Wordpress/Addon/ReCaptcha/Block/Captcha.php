<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress_Addon_ReCaptcha
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ReCaptcha_Block_Captcha extends Mage_Core_Block_Text
{
	/**
	 * Determine whether the plugin is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::helper('wordpress')->isPluginEnabled('wp-recaptcha/wp-recaptcha.php');
	}

	/**
	 * Set the ReCaptcha HTML
	 *
	 * @return $this
	 */
	public function _beforeToHtml()
	{
		if ($this->isEnabled()) {
			$this->setText($this->getRecaptchaHtml());

			return parent::_beforeToHtml();
		}
		
		return false;
	}

	/**
	 * Retrieve the ReCaptcha HTML
	 *
	 * @return string
	 */
	public function getRecaptchaHtml()
	{
		if (!$this->isEnabled() || !$this->getPublicKey()) {
			return '';
		}

		if (Mage::getSingleton('customer/session')->isLoggedIn() && $this->getPluginOption('bypass_for_registered_users')) {
			return '';
		}

    return Mage::helper('wp_addon_recaptcha/core')->simulatedCallback(function() {
      ob_start();
      $plugin = new ReCAPTCHAPlugin('recaptcha_options');
      $plugin->show_recaptcha_in_comments();

      return ob_get_clean();
    });
	}
	
	/**
	 * Retrieve the public key
	 *
	 * @return string
	 */
	public function getPublicKey()
	{
		return $this->getPluginOption('site_key');
	}
	
	/**
	 * Retrieve a plugin option
	 *
	 * @param string $key = null
	 * @return mixed
	 */
	public function getPluginOption($key = null)
	{
		return Mage::helper('wordpress')->getPluginOption('recaptcha_options', $key);
	}
}
