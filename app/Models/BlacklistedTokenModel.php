<?php

namespace App\Models;

use CodeIgniter\Model;

class BlacklistedTokenModel extends Model
{
    protected $table = 'blacklisted_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['token'];
    protected $useTimestamps = true;
}
