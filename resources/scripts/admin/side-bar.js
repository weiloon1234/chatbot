export default [
    {name: 'Dashboard', route: 'admin.home', icon: 'fa-home'},
    {
        name: 'User',
        icon: 'fa-user',
        children: [
            {name: 'Manage user', route: 'admin.user.user.list', permissions: ['Manage user']},
            {name: 'Manage user credit', route: 'admin.user.credit.list', permissions: ['Manage user credit']},
            {name: 'Manage user deposit', route: 'admin.user.deposit.list', permissions: ['Manage user deposit'], notification: 'pending_user_deposit'},
            {name: 'Manage user withdrawal', route: 'admin.user.withdrawal.list', permissions: ['Manage user withdrawal'], notification: 'pending_user_withdrawal'},
        ],
    },
    {
        name: 'Article',
        icon: 'fa-book',
        children: [
            {name: 'Manage article', route: 'admin.article.article.list', permissions: ['Manage article']},
            {name: 'Manage article category', route: 'admin.article.category.list', permissions: ['Manage article category']},
        ],
    },
    {
        name: 'Management',
        icon: 'fa-user-secret',
        children: [
            {name: 'Manage admin', route: 'admin.management.admin.list', permissions: ['Manage admin']},
            {name: 'Manage admin group', route: 'admin.management.admin.group.list', permissions: ['Manage admin group']},
        ],
    },
    {
        name: 'Other',
        icon: 'fa-cogs',
        children: [
            {name: 'Manage setting', route: 'admin.other.setting.list', permissions: ['Manage setting']},
            {name: 'Manage page', route: 'admin.other.page.list', permissions: ['Manage page']},
            {name: 'Manage country', route: 'admin.other.country.list', permissions: ['Manage country']},
            {name: 'Manage bank', route: 'admin.other.bank.list', permissions: ['Manage bank']},
            {name: 'Manage company bank', route: 'admin.other.company-bank.list', permissions: ['Manage company bank']},
            {name: 'Audit trail', route: 'admin.other.audit-trail.list', permissions: ['Audit trail']},
        ],
    },
];
