// AUTO-GENERATED DOMAIN ENUMERATIONS - DO NOT EDIT
export default {
    "Admin": {
        "type": [
            "Admin",
            "Super admin",
            "Developer"
        ]
    },
    "AdminAdjustUserCredit": {
        "transaction_type": {
            "1": "Admin add",
            "2": "Admin subtract"
        },
        "credit_type": {
            "1": "Credit 1",
            "2": "Credit 2"
        }
    },
    "AdminGroup": {
        "permission_tag": {
            "Manage user": "Manage user",
            "Manage user credit": "Manage user credit",
            "Manage user deposit": "Manage user deposit",
            "Manage user withdrawal": "Manage user withdrawal",
            "Manage article category": "Manage article category",
            "Manage article": "Manage article",
            "Manage setting": "Manage setting",
            "Manage page": "Manage page",
            "Manage country": "Manage country",
            "Manage bank": "Manage bank",
            "Manage company bank": "Manage company bank",
            "Manage admin": "Manage admin",
            "Manage admin group": "Manage admin group",
            "Audit trail": "Audit trail",
            "Export": "Export",
            "Dashboard statistics": "Dashboard statistics"
        }
    },
    "AdminGroupPermission": {
        "permission_tag": {
            "Manage user": "Manage user",
            "Manage user credit": "Manage user credit",
            "Manage user deposit": "Manage user deposit",
            "Manage user withdrawal": "Manage user withdrawal",
            "Manage article category": "Manage article category",
            "Manage article": "Manage article",
            "Manage setting": "Manage setting",
            "Manage page": "Manage page",
            "Manage country": "Manage country",
            "Manage bank": "Manage bank",
            "Manage company bank": "Manage company bank",
            "Manage admin": "Manage admin",
            "Manage admin group": "Manage admin group",
            "Audit trail": "Audit trail",
            "Export": "Export",
            "Dashboard statistics": "Dashboard statistics"
        }
    },
    "ArticleCategory": {
        "main_display_style": {
            "1": "4 box",
            "2": "6 box",
            "11": "Carousel view",
            "21": "List view"
        },
        "main_display_show_more": [
            "No",
            "Yes"
        ],
        "main_display_show_title": [
            "No",
            "Yes"
        ],
        "details_show_article_cover": [
            "No",
            "Yes"
        ],
        "details_show_article_datetime": [
            "No",
            "Yes"
        ],
        "list_display_style": {
            "1": "Grid view",
            "21": "List view",
            "31": "Masonry view"
        }
    },
    "Country": {
        "status": [
            "Inactive",
            "Active"
        ]
    },
    "Deposit": {
        "role": {
            "user": "User",
            "merchant": "Merchant"
        },
        "user_credit_type": {
            "1": "Credit 1",
            "2": "Credit 2"
        },
        "user_deposit_method": {
            "99": "Manual"
        },
        "merchant_deposit_method": [],
        "status": [
            "Pending",
            "Completed",
            "Canceled"
        ]
    },
    "Setting": {
        "setting_type": {
            "select": "Select",
            "text": "Text",
            "textarea": "Textarea",
            "editor": "Editor",
            "number": "Number",
            "fund": "Fund",
            "image": "Image"
        }
    },
    "User": {
        "credit_type": {
            "1": "Credit 1",
            "2": "Credit 2"
        }
    },
    "UserCreditTransaction": {
        "transaction_type": {
            "1": "Admin add",
            "2": "Admin subtract",
            "3": "Transaction deposit",
            "11": "Transaction transfer credit out",
            "12": "Transaction transfer credit in",
            "21": "Transaction withdraw",
            "22": "Transaction withdraw refund"
        }
    },
    "Withdrawal": {
        "role": {
            "user": "User",
            "merchant": "Merchant"
        },
        "user_credit_type": {
            "1": "Credit 1",
            "2": "Credit 2"
        },
        "user_withdraw_method": {
            "99": "Manual"
        },
        "merchant_withdraw_method": [],
        "status": [
            "Pending",
            "Completed",
            "Canceled"
        ]
    }
};
