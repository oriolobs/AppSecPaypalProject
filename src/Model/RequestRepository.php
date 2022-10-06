<?php
declare(strict_types=1);
namespace Pw\SlimApp\Model;

interface RequestRepository
{
    public function postRequest($money_request): void;

    public function getUnpaidRequests($email): void;

    public function checkRequestReceiver($email, $id_request): bool;

    public function checkEnoughMoney($email, $id_request): bool;

    public function acceptRequest($email, $id_request): void;
}