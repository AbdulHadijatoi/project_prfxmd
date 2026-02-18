<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $st_account_id
 * @property integer $login
 * @property integer $serverId
 * @property integer $userId
 * @property boolean $canBeLeader
 * @property string $leaderBio
 * @property float $leaderFeeSubscriptionAmount
 * @property float $leaderFollowingFeePercent
 * @property float $leaderFollowingMinFreeMargin
 * @property boolean $leaderPerformanceFeeType
 * @property float $leaderPerformanceFeePercent
 * @property string $updated_by_type
 * @property string $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class STAccounts extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'st_accounts';

    /**
     * @var array
     */
    protected $fillable = ['st_account_id', 'login', 'serverId', 'userId', 'canBeLeader', 'leaderBio', 'leaderFeeSubscriptionAmount', 'leaderFollowingFeePercent', 'leaderFollowingMinFreeMargin', 'leaderPerformanceFeeType', 'leaderPerformanceFeePercent', 'updated_by_type', 'updated_by', 'created_at', 'updated_at'];
}
