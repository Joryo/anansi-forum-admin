<?php

namespace App\Models;

/**
 * Member model
 */
class Member extends ApiModel
{
    public $type = 'member';

    /**
     * Update a single member
     * @param int $id - Id of the member
     * @param array $data - Data of the member
     * 
     * @return int status code
     */
    public function update($id, $data = [])
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        parent::update($id, $data);
    }

    /**
     * Get admin members
     * @param int $offset
     * 
     * @return Array
     */
    public function getAdmins($offset)
    {
        return $this->getFiltered($offset, 'role', 'admin');
    }
}
