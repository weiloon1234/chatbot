import CommonLang from '@/en.json';
import ZhLang from '@/zh.json';
import SharedLang from './shared-lang.json';

export default {
    ...Object.assign(
        {},
        ...Object.keys(ZhLang).map((key) => {
            return {
                [`${key}`]: key,
            };
        })
    ),
    ...CommonLang,
    ...SharedLang,
};
