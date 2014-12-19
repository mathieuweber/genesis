<?php
require_once ('Gen/Entity/Abstract.php');

require_once('Gen/Entity/Date.php');
	
/**
 * @category   Trust
 * @package    Trust_Model
 */
abstract class User_Base extends Gen_Entity_Abstract
{

	protected $_email;

	protected $_password;

	protected $_firstName;

	protected $_lastName;

	protected $_avatar;

	protected $_admin;

	protected $_createdOn;

	protected $_updatedOn;

	protected $_deleted;

	public function getEmail()
	{
		return $this->_email;
	}

	public function setEmail($email)
	{
		$this->_email = (null !== $email) ? (string) $email : null;
		return $this;
	}

	public function getPassword()
	{
		return $this->_password;
	}

	public function setPassword($password)
	{
		$this->_password = (null !== $password) ? (string) $password : null;
		return $this;
	}

	public function getFirstName()
	{
		return $this->_firstName;
	}

	public function setFirstName($firstName)
	{
		$this->_firstName = (null !== $firstName) ? (string) $firstName : null;
		return $this;
	}

	public function getLastName()
	{
		return $this->_lastName;
	}

	public function setLastName($lastName)
	{
		$this->_lastName = (null !== $lastName) ? (string) $lastName : null;
		return $this;
	}

	public function getAvatar()
	{
		return $this->_avatar;
	}

	public function setAvatar($avatar)
	{
		$this->_avatar = (null !== $avatar) ? (string) $avatar : null;
		return $this;
	}

	public function getAdmin()
	{
		return $this->_admin;
	}

	public function setAdmin($admin)
	{
		$this->_admin = (null !== $admin) ? ((bool) $admin ? 1 : 0) : null;
		return $this;
	}

	public function getCreatedOn()
	{
		if(null === $this->_createdOn) {
			$this->_createdOn = new Gen_Entity_Date();
		}
		return $this->_createdOn;
	}

	public function setCreatedOn($createdOn)
	{
		$this->getCreatedOn()->update($createdOn);
		return $this;
	}

	public function getUpdatedOn()
	{
		if(null === $this->_updatedOn) {
			$this->_updatedOn = new Gen_Entity_Date();
		}
		return $this->_updatedOn;
	}

	public function setUpdatedOn($updatedOn)
	{
		$this->getUpdatedOn()->update($updatedOn);
		return $this;
	}

	public function getDeleted()
	{
		return $this->_deleted;
	}

	public function setDeleted($deleted)
	{
		$this->_deleted = (null !== $deleted) ? ((bool) $deleted ? 1 : 0) : null;
		return $this;
	}
}