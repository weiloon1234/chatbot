import Shared from '#/shared/lang/en/index.js';
import PermissionZh from '@/zh_permission.json';
import Lang from './lang.json';
import PermissionEn from '@/en_permission.json';

export default {
    ...Shared,
    ...Object.assign(
        {},
        ...Object.keys(PermissionZh).map(key => {
            return {
                [`${key}`]: key,
            };
        }),
    ),
    ...Lang,
    ...PermissionEn,
};
