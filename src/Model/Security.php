<?php

declare(strict_types=1);

namespace Pw\SlimApp\Model;

final class Security{

    private string $old_password;
    private string $new_password;
    private string $confirmed_password;

    public function __construct(string $old_password, string $new_password, string $confirmed_password){
        $this->old_password = md5($old_password);
        $this->new_password = md5($new_password);
        $this->confirmed_password = md5($confirmed_password);
    }

    public function resetPassword(string $database_password): bool{
      $ok = TRUE;

     if($database_password != $this->old_password || $this->old_password == $this->new_password ||
        $this->new_password != $this->confirmed_password){
          $ok = FALSE;
      } //if

      return $ok;
    }

    public function old_password(): string
    {
        return $this->old_password;
    }

    public function setOldPassword(string $old_password): void
    {
        $this->old_password = $old_password;
    }

    public function new_password(): string
    {
        return $this->new_password;
    }

    public function setNewPassword(string $new_password): void
    {
        $this->new_password = $new_password;
    }

    public function confirmed_password(): string
    {
        return $this->confirmed_password;
    }

    public function setConfirmedPassword(string $confirmed_password): void
    {
        $this->confirmed_password = $confirmed_password;
    }
}
