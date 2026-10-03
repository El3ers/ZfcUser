<?php

namespace ZfcUser\Mapper;

interface IdentityInterface
{

    public function findById($id);

    public function insert($identity);

    public function update($identity);
}
