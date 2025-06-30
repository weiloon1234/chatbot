export default class NumericDirective {
    constructor(input, binding) {
        Object.assign(this, {
            input,
            binding,
        });
        input.addEventListener('keydown', this);
        input.addEventListener('change', this);
    }

    static install(Vue) {
        Vue.directive('decimal', {
            mounted(el, binding, vNode) {
                binding.name = 'decimal';
                return new NumericDirective(vNode.el, binding);
            },
        });
        Vue.directive('integer', {
            mounted(el, binding, vNode) {
                binding.name = 'integer';
                return new NumericDirective(vNode.el, binding);
            },
        });
    }

    handleEvent(event) {
        this[event.type](event);
    }

    keydown(event) {
        const { target, key, keyCode, ctrlKey, metaKey } = event;

        if (
            !(
                // Is numeric
                (
                    (key >= '0' && key <= '9') ||
                    // Is special symbol allowed (. and -)
                    (((key === '.' && this.binding.name === 'decimal') || (key === '-' && !this.binding.modifiers.unsigned)) && !~target.value.indexOf(key)) ||
                    // Is system key
                    [
                        'Delete',
                        'Backspace',
                        'Tab',
                        'Esc',
                        'Escape',
                        'Enter',
                        'Home',
                        'End',
                        'PageUp',
                        'PageDown',
                        'Del',
                        'Delete',
                        'Left',
                        'ArrowLeft',
                        'Right',
                        'ArrowRight',
                        'Insert',
                        'Up',
                        'ArrowUp',
                        'Down',
                        'ArrowDown',
                    ].includes(key) ||
                    // Is ctrl + a, c, x, v
                    ((ctrlKey || metaKey) && [65, 67, 86, 88].includes(keyCode))
                )
            )
        ) {
            event.preventDefault();
        }
    }

    change({ target }) {
        const isDecimal = this.binding.name === 'decimal';
        let value = target.value;
        if (!value) {
            return;
        }
        // Is it a negative number and is it allowed?
        const isNegative = /^\s*-/.test(value) && !this.binding.modifiers.unsigned;
        // Remove invalid digits (if it's a decimal, then allows "," and "." to stay)
        value = value.replace(isDecimal ? /[^\d,.]/g : /\D/g, '');
        if (isDecimal) {
            // Naive adjustment for decimal values, breaks the number by ",." and considers the last group as the decimal part
            const pieces = value.split(/[,.]/);
            // Removes useless zeroes on the right
            const decimal = pieces.pop().replace(/0+$/, '');
            if (pieces.length) {
                value = `${pieces.join('') || (decimal ? '0' : '')}${decimal ? `.${decimal}` : ''}`;
            }
        }
        // Removes useless zeroes on the left
        value = value.replace(/^(?:0(?!\b))+/, '');
        if (value && isNegative) {
            value = `-${value}`;
        }
        // Raise a fake event to signal others that we've updated the field
        if (target.value !== value) {
            target.value = value;
            const event = document.createEvent('UIEvent');
            event.initEvent('input', true, false, window, 0);
            target.dispatchEvent(event);
        }
    }
}
