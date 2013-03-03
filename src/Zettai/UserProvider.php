<?php
namespace Zettai;
 
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Создаёт объект пользователя по логину.
 * Навеяно статьёй http://www.johannreinke.com/en/2012/08/28/mysql-authentication-in-silex-the-php-micro-framework/
**/
class UserProvider implements UserProviderInterface
{
    public function __construct(Config $config)
    // public function __construct(Config $userlist)
    {
        $this->config = $config;
        // $this->userlist = $userlist;
    }
 
    public function loadUserByUsername($username)
    {
        if (! isset ($this->config->security[$username])) {
        // if (! isset ($this->userlist[$username])) {
            throw new UsernameNotFoundException('Пользователя "' . $username . '" не существует');
        }
        $userdata = $this->config->security[$username];
        // $userdata = $this->userlist[$username];
        return new User($username, $userdata->password, $userdata->roles->toArray());
    }
 
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }
 
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
