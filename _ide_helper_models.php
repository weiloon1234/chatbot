<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $model
 * @property int $model_id
 * @property string $login_type
 * @property string $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed $params
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountLoginLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountLoginLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountLoginLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountLoginLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountLoginLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountLoginLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountLoginLog whereLoginType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountLoginLog whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountLoginLog whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountLoginLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAccountLoginLog {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $admin_id
 * @property int|null $user_id
 * @property string $operation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin|null $admin
 * @property mixed $params
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountStatusLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountStatusLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountStatusLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountStatusLog whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountStatusLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountStatusLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountStatusLog whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountStatusLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountStatusLog whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAccountStatusLog {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $username
 * @property string|null $email
 * @property string|null $name
 * @property string $password
 * @property int|null $admin_group_id
 * @property int $type
 * @property string $lang
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\AdminGroup|null $group
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin listForAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin listForDeveloper()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin listForSuperAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereAdminGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAdmin {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $admin_id
 * @property int|null $user_credit_transaction_id
 * @property int $credit_type
 * @property int $transaction_type
 * @property float $amount
 * @property string|null $related_key
 * @property array<array-key, mixed>|null $params
 * @property string|null $remark
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin|null $admin
 * @property-read AdminAdjustUserCredit|null $transaction
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereCreditType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereRelatedKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereUserCreditTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminAdjustUserCredit whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAdminAdjustUserCredit {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $group_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Admin> $admins
 * @property-read int|null $admins_count
 * @property mixed $params
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdminGroupPermission> $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroup whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAdminGroup {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $admin_group_id
 * @property string $permission_tag
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed $params
 * @property-read \App\Models\AdminGroup $group
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroupPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroupPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroupPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroupPermission whereAdminGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroupPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroupPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroupPermission wherePermissionTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminGroupPermission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAdminGroupPermission {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $sorting
 * @property int|null $article_category_id
 * @property string|null $subject_en
 * @property string|null $subject_zh
 * @property string|null $description_en
 * @property string|null $description_zh
 * @property string|null $content_en
 * @property string|null $content_zh
 * @property string|null $cover_en
 * @property string|null $cover_zh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ArticleCategory|null $category
 * @property mixed $params
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article sorted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereArticleCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereContentEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereContentZh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCoverEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCoverZh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereDescriptionZh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereSorting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereSubjectEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereSubjectZh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperArticle {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name_en
 * @property string $name_zh
 * @property int|null $main_display_style
 * @property int|null $main_display_show_more
 * @property int $main_display_show_title
 * @property int|null $list_display_style
 * @property int|null $details_show_article_cover
 * @property int|null $details_show_article_datetime
 * @property int $sorting
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property mixed $params
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory sorted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereDetailsShowArticleCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereDetailsShowArticleDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereListDisplayStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereMainDisplayShowMore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereMainDisplayShowTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereMainDisplayStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereNameZh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereSorting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperArticleCategory {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $operation
 * @property int|null $admin_id
 * @property string $model_class
 * @property int $model_id
 * @property string $description
 * @property array<array-key, mixed>|null $created_data
 * @property array<array-key, mixed>|null $old_data
 * @property array<array-key, mixed>|null $edited_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin|null $admin
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereCreatedData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereEditedData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereModelClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereOldData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAuditTrail {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $country_id
 * @property string $name_en
 * @property string $name_zh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Country|null $country
 * @property mixed $params
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereNameZh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBank {}
}

namespace App\Models{
/**
 * @property mixed $params
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBaseModel {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $bank_id
 * @property int|null $country_id
 * @property string $name_en
 * @property string $name_zh
 * @property string|null $account_name
 * @property string|null $account_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\Country|null $country
 * @property mixed $params
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank whereAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank whereNameZh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyBank withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCompanyBank {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $iso2
 * @property string $iso3
 * @property string $name
 * @property string|null $phone_code
 * @property string|null $currency_code
 * @property string|null $currency_symbol_prefix
 * @property string|null $currency_symbol_suffix
 * @property int $currency_decimal
 * @property float|null $rate_to_base
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CountryLocation> $area
 * @property-read int|null $area_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property mixed $params
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CountryLocation> $states
 * @property-read int|null $states_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCurrencyDecimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCurrencySymbolPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCurrencySymbolSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIso2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIso3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country wherePhoneCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereRateToBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCountry {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $country_id
 * @property int|null $parent_id
 * @property int $sorting
 * @property string|null $name_en
 * @property string|null $name_zh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CountryLocation> $area
 * @property-read int|null $area_count
 * @property-read \App\Models\Country|null $country
 * @property mixed $params
 * @property-read CountryLocation|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CountryLocation> $sons
 * @property-read int|null $sons_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation area()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation sorted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation state()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation whereNameZh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation whereSorting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryLocation withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCountryLocation {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $role
 * @property int|null $user_id
 * @property int|null $admin_id
 * @property int $credit_type
 * @property float $credit_amount
 * @property float $admin_fees
 * @property float $conversion_rate
 * @property float $currency_amount
 * @property int|null $country_id
 * @property int|null $company_bank_id
 * @property int|null $bank_id
 * @property string|null $bank_account_holder_name
 * @property string|null $bank_account_number
 * @property int|null $deposit_method
 * @property int $status
 * @property string|null $related_key
 * @property string|null $params
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin|null $admin
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\CompanyBank|null $companyBank
 * @property-read \App\Models\Country|null $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DepositReceipt> $receipts
 * @property-read int|null $receipts_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereAdminFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereBankAccountHolderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereCompanyBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereConversionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereCreditAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereCreditType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereCurrencyAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereDepositMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereRelatedKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDeposit {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $deposit_id
 * @property string $file_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Deposit|null $deposit
 * @property mixed $params
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepositReceipt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepositReceipt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepositReceipt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepositReceipt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepositReceipt whereDepositId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepositReceipt whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepositReceipt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepositReceipt whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDepositReceipt {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $tag
 * @property string $title
 * @property string|null $content_en
 * @property string|null $content_zh
 * @property int $is_system
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property mixed $params
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereContentEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereContentZh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPage {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $setting
 * @property string $setting_name
 * @property string|null $setting_value
 * @property string $setting_type
 * @property array<array-key, mixed>|null $params
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSettingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSettingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSettingValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSetting {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $username
 * @property string|null $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $password2
 * @property int|null $country_id
 * @property int|null $contact_country_id
 * @property string|null $contact_number
 * @property string|null $full_contact_number
 * @property int|null $introducer_user_id
 * @property string $lang
 * @property string|null $avatar
 * @property float $credit_1
 * @property float $credit_2
 * @property float $credit_3
 * @property float $credit_4
 * @property float $credit_5
 * @property int|null $bank_id
 * @property string|null $bank_account_name
 * @property string|null $bank_account_number
 * @property string|null $national_id
 * @property int $first_login
 * @property \Illuminate\Support\Carbon|null $ban_until
 * @property int $ban
 * @property \Illuminate\Support\Carbon|null $new_login_at
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property int|null $unilevel
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\Country|null $contactCountry
 * @property-read \App\Models\Country|null $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserCreditTransaction> $creditTransactions
 * @property-read int|null $credit_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserUnilevel> $downlines
 * @property-read int|null $downlines_count
 * @property-read mixed $identity
 * @property-read User|null $introducer
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserUnilevel> $unilevels
 * @property-read int|null $unilevels_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserUnilevel> $uniqueDownlines
 * @property-read int|null $unique_downlines_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserUnilevel> $uniqueUnilevels
 * @property-read int|null $unique_unilevels_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBanUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereContactCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCredit1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCredit2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCredit3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCredit4($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCredit5($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIntroducerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNewLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUnilevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int $credit_type
 * @property int $transaction_type
 * @property float $amount
 * @property string|null $related_key
 * @property array<array-key, mixed>|null $params
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminAdjustUserCredit|null $adminAdjustUserCredit
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction whereCreditType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction whereRelatedKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransaction whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserCreditTransaction {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $from_user_id
 * @property int|null $to_user_id
 * @property int $from_credit_type
 * @property int $to_credit_type
 * @property float $conversion_rate
 * @property float $from_amount
 * @property float $to_amount
 * @property string|null $related_key
 * @property string|null $params
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $fromUser
 * @property-read \App\Models\User|null $toUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereConversionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereFromAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereFromCreditType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereRelatedKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereToAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereToCreditType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereToUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCreditTransfer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserCreditTransfer {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int $user_unilevel
 * @property int|null $introducer_user_id
 * @property int $introducer_unilevel
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed $params
 * @property-read \App\Models\User|null $introducer
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUnilevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUnilevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUnilevel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUnilevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUnilevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUnilevel whereIntroducerUnilevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUnilevel whereIntroducerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUnilevel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUnilevel whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUnilevel whereUserUnilevel($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserUnilevel {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $admin_id
 * @property string|null $session_id
 * @property int|null $contact_country_id
 * @property string|null $contact_number
 * @property string|null $full_contact_number
 * @property string|null $message
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin|null $admin
 * @property-read \App\Models\Country|null $contactCountry
 * @property mixed $params
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage whereContactCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage whereFullContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsappMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWhatsappMessage {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $role
 * @property int|null $user_id
 * @property int|null $admin_id
 * @property int $credit_type
 * @property float $credit_amount
 * @property float $admin_fees
 * @property float $conversion_rate
 * @property float $currency_amount
 * @property float $receivable_currency_amount
 * @property int|null $country_id
 * @property int|null $bank_id
 * @property string|null $bank_account_holder_name
 * @property string|null $bank_account_number
 * @property int|null $withdraw_method
 * @property int $status
 * @property string|null $related_key
 * @property string|null $params
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin|null $admin
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\Country|null $country
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereAdminFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereBankAccountHolderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereConversionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereCreditAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereCreditType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereCurrencyAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereReceivableCurrencyAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereRelatedKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Withdrawal whereWithdrawMethod($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWithdrawal {}
}

