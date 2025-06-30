import BigNumber from 'bignumber.js';
import moment from 'moment';
import Swal from 'sweetalert2';
import $i18n from "#/admin/i18n.js";
import DomainEnums from '#/domain-enums.js';

export default {
    ucfirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    },
    async alertSuccess({ message, callback = null, message_is_html = false }) {
        let obj = {
            icon: 'success',
        };
        if (message_is_html) {
            obj.html = message;
        } else {
            obj.text = message;
        }

        Swal.fire(obj).then((result) => {
            if (typeof callback === 'function') {
                callback(result);
            }
        });
    },
    async alertError({ message, callback = null, message_is_html = false }) {
        let obj = {
            icon: 'error',
        };
        if (message_is_html) {
            obj.html = message;
        } else {
            obj.text = message;
        }

        Swal.fire(obj).then((result) => {
            if (typeof callback === 'function') {
                callback(result);
            }
        });
    },
    async alertWarning({ message, callback = null, message_is_html = false }) {
        let obj = {
            icon: 'warning',
        };
        if (message_is_html) {
            obj.html = message;
        } else {
            obj.text = message;
        }

        Swal.fire(obj).then((result) => {
            if (typeof callback === 'function') {
                callback(result);
            }
        });
    },
    async alertConfirm({ message = null, callback = null, message_is_html = false }) {
        let obj = {
            icon: 'warning',
        };

        if (message === null) {
            $i18n.global.t('This action cannot undone');
        }
        if (message_is_html) {
            obj.html = message;
        } else {
            obj.text = message;
        }

        obj.title = $i18n.global.t('Are you sure') + '?';
        obj.showCancelButton = true;
        obj.confirmButtonColor = '#3085d6';
        obj.cancelButtonColor = '#d33';
        obj.confirmButtonText = $i18n.global.t('Yes');
        obj.cancelButtonText = $i18n.global.t('Cancel');

        Swal.fire(obj).then((result) => {
            if (typeof callback === 'function') {
                callback(result);
            }
        });
    },
    distanceFormat: function (distance) {
        distance = parseFloat(distance);
        if (distance <= 1) {
            return '<1 km';
        } else {
            return distance.toFixed(1) + ' km';
        }
    },
    dateFormat: function (dt) {
        return moment(dt).format('YYYY-MM-DD');
    },
    timeFormat: function (dt) {
        return moment(dt).format('hh:mm:ss A');
    },
    dateTimeFormat: function (dt) {
        return moment(dt).format('YYYY-MM-DD hh:mm:ss A');
    },
    fundFormat: function (amount, decimal, separator) {
        // Handle invalid `amount` input
        if (isNaN(amount) || amount === null || amount === undefined) {
            amount = 0; // Set to zero if the input is invalid
        }

        // Ensure `decimal` is a valid number, fallback to default config
        if (typeof decimal !== 'number' || decimal < 0 || !Number.isInteger(decimal)) {
            decimal = 2; // Use default config or 2 as fallback
        }

        // Convert the amount to a BigNumber to handle precise operations
        amount = new BigNumber(amount);

        // Split into integer and fractional parts
        let parts = amount.toString().split('.');

        // Format integer part with thousands separator
        if (separator === true || typeof separator === 'undefined') {
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        // Format the fractional part
        if (decimal === 0) {
            return parts[0]; // No decimal part needed
        }

        const fractionalPart = parts[1] ? parts[1].slice(0, decimal) : '0'.repeat(decimal);

        return `${parts[0]}.${fractionalPart.padEnd(decimal, '0')}`;
    },
    optionalDecimalFormat: function (amount, decimal) {
        if (typeof decimal === 'undefined') {
            decimal = window.ROLE_CONFIG.DECIMAL_POINT;
        }

        return (amount * 1).toFixed(decimal).replace(/[.,]00$/, '');
    },
    isImage(str) {
        // Use URL parsing for better path extraction
        try {
            const url = new URL(str);
            const pathname = url.pathname.toLowerCase();
            return /\.(jpg|jpeg|png|gif|bmp|webp)$/.test(pathname);
        } catch {
            // Fallback for relative paths or invalid URLs
            return /\.(jpg|jpeg|png|gif|bmp|webp)(?=[?#]|$)/i.test(str);
        }
    },
    deepCopy(obj) {
        if (obj === null || typeof obj !== 'object') {
            return obj;
        }

        const copy = Array.isArray(obj) ? [] : {};

        Object.keys(obj).forEach((key) => {
            copy[key] = this.deepCopy(obj[key]);
        });

        return copy;
    },
    camelCase(str) {
        let arr = str.split(/[_-]/);
        let newStr = '';
        for (let i = 1; i < arr.length; i++) {
            newStr += arr[i].charAt(0).toUpperCase() + arr[i].slice(1);
        }
        return arr[0] + newStr;
    },
    pascalCase(str) {
        let arr = this.camelCase(str);
        return arr[0].toUpperCase() + arr.slice(1);
    },
    randomInteger(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    },
    randomString(length = 10) {
        let result = '';
        let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let charactersLength = characters.length;
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    },
    guidGenerator() {
        let S4 = function () {
            return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
        };
        return S4() + S4() + '-' + S4() + '-' + S4() + '-' + S4() + '-' + S4() + S4() + S4();
    },
    pad(n, width, z) {
        z = z || '0';
        n = n + '';
        return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
    },
    getCurrentDomain() {
        return window.location.protocol + '//' + window.location.hostname;
    },
    safeImageUrl(url) {
        let entities = [
            ['%3F', '?'],
            ['%3D', '='],
        ];
        for (let i = 0, max = entities.length; i < max; ++i) url = url.replace(new RegExp(entities[i][0], 'ig'), entities[i][1]);

        return url;
    },
    async adjustYoutubeIframe() {
        return new Promise((resolve) => {
            if (document.getElementsByTagName('iframe').length) {
                for (let iframe of document.getElementsByTagName('iframe')) {
                    const regex = /^(https?:\/\/)?((www\.)?youtube\.com|youtu\.?be)\/.+$/gm;
                    if (iframe.src && regex.test(iframe.src)) {
                        let container = iframe.closest('p');
                        if (!container) {
                            container = iframe.closest('div');
                        }

                        if (container) {
                            if (iframe.clientWidth > container.clientWidth) {
                                let ratio = (container.clientWidth / iframe.clientWidth) * 100;
                                iframe.width = (iframe.clientWidth / 100) * ratio;
                                iframe.height = (iframe.clientHeight / 100) * ratio;
                            }
                        }
                    }
                }
            }

            resolve();
        });
    },
    editorJsParser(v) {
        if (!v) return null;
        if (v === 'null') return null;
        if (typeof v !== 'object') {
            try {
                v = JSON.parse(v);
            } catch (e) {
                console.log(e);
                return v;
            }
        }

        if (Array.isArray(v.blocks) && v.blocks.length) {
            let convertedHtml = '<div class="editor-content">';
            v.blocks.forEach((block) => {
                switch (block.type) {
                    case 'header':
                        convertedHtml += `<h${block.data.level}>${block.data.text}</h${block.data.level}>`;
                        break;
                    case 'embed':
                        convertedHtml += `<div><iframe width="${block.data.width}" height="${block.data.height}" src="${block.data.embed}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>`;
                        if (block.data.caption) {
                            convertedHtml += `<div class="border border-gray-200 p-2 rounded-md mt-1">${block.data.caption}</div>`;
                        }
                        break;
                    case 'paragraph':
                        if (!block.data.text) {
                            convertedHtml += `<p>     </p>`;
                        } else {
                            convertedHtml += `<p>${block.data.text}</p>`;
                        }
                        break;
                    case 'delimiter':
                        convertedHtml += '<hr />';
                        break;
                    case 'image':
                        convertedHtml += `<div><img class="img-fluid" src="${block.data.file.url}" /></div>`;
                        if (block.data.caption) {
                            convertedHtml += `<div class="border border-gray-200 p-2 rounded-md mt-1">${block.data.caption}</div>`;
                        }
                        break;
                    case 'list':
                        if (block.data.style === 'unordered') {
                            convertedHtml += '<ul class="pl-3" style="list-style: disc">';
                        } else {
                            convertedHtml += '<ul class="pl-3" style="list-style: decimal">';
                        }

                        block.data.items.forEach(function (li) {
                            convertedHtml += `<li>${li}</li>`;
                        });
                        convertedHtml += '</ul>';
                        break;
                    case 'link':
                        convertedHtml += `<div><a class="text-primary" href="${block.data.link}">${block.data.link}</a></div>`;
                        break;
                    case 'table':
                        convertedHtml += '<div class="w-full custom-table">';
                        convertedHtml += '<div class="overflow-x">';
                        if (block.data && block.data.content && block.data.content instanceof Array) {
                            convertedHtml += '<table class="table table-auto overflow-scroll w-full border-collapse border border-slate-300">';
                            convertedHtml += '<tbody>';
                            block.data.content.forEach((row) => {
                                convertedHtml += '<tr>';
                                row.forEach((c) => {
                                    convertedHtml += `<td class="break-keep whitespace-nowrap border border-slate-300">${c}</td>`;
                                });
                                convertedHtml += '</tr>';
                            });
                            convertedHtml += '</tbody>';
                            convertedHtml += '</table>';
                        }
                        convertedHtml += '</div>';
                        convertedHtml += '</div>';
                        break;
                    default:
                        console.log('Unknown block type', block.type);
                        break;
                }
            });

            convertedHtml += '</div>';
            return convertedHtml;
        }

        return null;
    },
    isValidContactNumber(contact_country_id, contact_number) {
        return parseInt(contact_country_id) > 0 && contact_number.length >= 8;
    },
    guessIsURL(str) {
        return str.startsWith('http://') || str.startsWith('https://') || str.startsWith('www.');
    },
    validURL(str) {
        let pattern = new RegExp(
            '^(https?:\\/\\/)?' + // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
                '(\\#[-a-z\\d_]*)?$',
            'i'
        ); // fragment locator
        return !!pattern.test(str);
    },
    stripTags(str) {
        return str.replace(/(<([^>]+)>)/gi, '');
    },
    getLanguages() {
        return {
            en: {
                flag: '/img/flags/us.svg',
                text: 'English',
                locale: 'en',
            },
            zh: {
                flag: '/img/flags/zh.svg',
                text: '中文',
                locale: 'zh',
            },
        };
    },
    hasPermission($admin, permissions) {
        if ($admin && (parseInt($admin.type) === 1 || parseInt($admin.type) === 2)) {
            return true;
        }

        const requiredPermissions = Array.isArray(permissions) ? permissions : [permissions];

        const hasPermissions = new Set(
            $admin?.group?.permissions?.map(r => r.permission_tag.toString()) || []
        );

        // Check if user has ALL required permissions (use .every())
        // or ANY required permission (use .some()) based on your needs
        return requiredPermissions.some(perm =>
            hasPermissions.has(perm.toString())
        );
    },
    async copyText(text, callback) {
        // Create new element
        let el = document.createElement('textarea');
        // Set value (string to be copied)
        el.value = text;
        // Set non-editable to avoid focus and move outside of view
        el.setAttribute('readonly', '');
        el.style = { position: 'absolute', left: '-9999px' };
        document.body.appendChild(el);
        // Select text inside element
        el.select();
        // Copy text to clipboard
        document.execCommand('copy');
        // Remove temporary element
        document.body.removeChild(el);

        if (typeof callback === 'function') {
            callback();
        }
    },
    async handleFormError(e) {
        let errors = {};
        if (e.response && e.response.data && e.response.data.errors) {
            errors = e.response.data.errors;
        } else if (e.response) {
            // Request made and server responded
            console.log(e.response.data);
            console.log(e.response.status);
            console.log(e.response.headers);
        } else if (e.request) {
            // The request was made but no response was received
            console.log(e.request);
        } else {
            console.log(e);
        }

        if (e.response && e.response.data && e.response.data.message) {
            await this.alertError({ message: e.response.data.message });
        } else if (e.message) {
            await this.alertError({ message: e.message });
        }

        return errors;
    },
    nl2br(str, is_xhtml) {
        if (typeof str === 'undefined' || str === null) {
            return '';
        }
        let breakTag = is_xhtml || typeof is_xhtml === 'undefined' ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    },
    extractFormData(formData) {
        let obj = {};

        if (formData instanceof FormData) {
            for (let pair of formData.entries()) {
                obj[pair[0]] = pair[1];
            }
        }
        return obj;
    },
    convertCountDownFromSeconds(seconds, returnType = 'pad_string') {
        if (seconds <= 0) return '00:00:00';

        let h = Math.floor(seconds / 3600);
        let m = Math.floor((seconds % 3600) / 60);
        let s = Math.floor((seconds % 3600) % 60);

        if (returnType === 'pad_string') {
            return `${this.pad(h, 2, '0')}:${this.pad(m, 2, '0')}:${this.pad(s, 2, '0')}`;
        } else if (returnType === 'string') {
            return `${h}:${m}:${s}`;
        } else {
            return {
                h: h,
                m: m,
                s: s,
            };
        }
    },
    arrayShuffle(array) {
        let currentIndex = array.length,
            randomIndex;

        // While there remain elements to shuffle.
        while (currentIndex > 0) {
            // Pick a remaining element.
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex--;

            // And swap it with the current element.
            [array[currentIndex], array[randomIndex]] = [array[randomIndex], array[currentIndex]];
        }

        return array;
    },
    compactNumberFormat(num) {
        let formatter = Intl.NumberFormat('en', {
            notation: 'compact',
            maximumFractionDigits: 2,
            roundingMode: 'floor',
        });
        return formatter.format(num);
    },
    isMobileDevice() {
        return /Mobi|Android/i.test(navigator.userAgent);
    },
    objectToFormData(obj, formData = new FormData(), parentKey = '') {
        Object.entries(obj).forEach(([key, value]) => {
            const fullKey = parentKey ? `${parentKey}[${key}]` : key;

            if (value instanceof FileList) {
                Array.from(value).forEach((file, index) => {
                    formData.append(`${fullKey}[${index}]`, file);
                });
            } else if (value instanceof File) {
                formData.append(fullKey, value);
            } else if (typeof value === 'object' && value !== null) {
                this.objectToFormData(value, formData, fullKey); // Recursively handle nested objects
            } else if (value !== null && value !== undefined) {
                formData.append(fullKey, value);
            }
        });

        return formData;
    },
    hasClassOrAncestor(element, className) {
        while (element) {
            if (element.classList?.contains(className)) {
                return true;
            }
            element = element.parentElement;
        }
        return false;
    },
    isValuesEqual(a, b) {
        // Handle null/undefined strictly
        if (a === null || a === undefined || b === null || b === undefined) {
            return a === b;
        }

        // Handle boolean values strictly
        if (typeof a === 'boolean' || typeof b === 'boolean') {
            return a === b;
        }

        // Attempt numeric comparison only if both are numeric-looking
        const isANumeric = !isNaN(a) && !isNaN(parseFloat(a));
        const isBNumeric = !isNaN(b) && !isNaN(parseFloat(b));

        if (isANumeric && isBNumeric) {
            return Number(a) === Number(b);
        }

        // Non-numeric values: compare as strings but preserve type distinction
        if (typeof a === typeof b) {
            return a.toString() === b.toString();
        }

        return a.toString() === b.toString() && a == b; // Use loose equality for final check
    },
    flatObjectToSelectOptions(obj) {
        return Object.entries(obj).map(([key, value]) => ({
            value: String(key),
            text: value,
        }));
    },
    validateEnumValue(enumPath, value) {
        const enumData = this.getObjectValueNested(DomainEnums, enumPath);
        return Object.prototype.hasOwnProperty.call(enumData, String(value));
    },
    getObjectValueNested(obj, path) {
        return path.split('.').reduce((o, p) => o?.[p] || {}, obj);
    },
    getEnumOptions(enumPath) {
        const enumData = this.getObjectValueNested(DomainEnums, enumPath);
        if (Array.isArray(enumData)) {
            return enumData.map((label, index) => {
                return {
                    value: String(index),
                    text: $i18n.global.t(label),
                };
            });
        } else {
            return Object.entries(enumData).map(([value, label]) => ({
                value: String(value),
                text: $i18n.global.t(label),
            }));
        }
    },
    explainCurrency(country, amount, format = true, decimal = 2) {
        let s = [];
        if (country.currency_prefix) {
            s.push(country.currency_prefix);
        }
        if (format) {
            s.push(this.fundFormat(amount, decimal));
        } else {
            s.push(amount);
        }
        if (country.currency_suffix) {
            s.push(country.currency_suffix);
        }
        return s.join('');
    }
};
