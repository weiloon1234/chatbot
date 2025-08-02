import { defineStore } from 'pinia'
import { ref, reactive, computed, nextTick, markRaw } from 'vue'

export default function createModalStore(namespace = 'modal') {
    return defineStore(`${namespace}ModalStore`, () => {
        // State
        const modals = ref(new Map())
        const currentModalId = ref(null)
        const globalModal = reactive({
            isOpen: false,
            component: null,
            props: {},
            options: {}
        })

        // Getters
        const isOpen = computed(() => globalModal.isOpen || modals.value.size > 0)
        const currentModal = computed(() => {
            // If there are stacked modals, return the topmost one
            if (modals.value.size > 0) {
                const topModalId = Array.from(modals.value.keys()).pop()
                return modals.value.get(topModalId)
            }
            // Otherwise return global modal if open
            if (globalModal.isOpen) return globalModal
            return null
        })
        
        // Check if we should show global modal (no stacked modals)
        const showGlobalModal = computed(() => globalModal.isOpen && modals.value.size === 0)
        const hasMultipleModals = computed(() => modals.value.size > 1)
        const modalCount = computed(() => modals.value.size + (globalModal.isOpen ? 1 : 0))

        // Actions - Enhanced for modal chaining
        const open = (component, props = {}, options = {}, onCloseCallback = null) => {
            // If a modal is already open, stack this one on top using multi-modal
            if (globalModal.isOpen || modals.value.size > 0) {
                const modalId = `modal-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
                return openModal(modalId, component, props, options, onCloseCallback)
            }
            
            // Otherwise use global modal for first modal
            globalModal.component = markRaw(component)
            Object.assign(globalModal.props, props)
            Object.assign(globalModal.options, {
                small: false,
                title: null,
                maxHeight: 600,
                onCloseCallback,
                ...options
            })
            globalModal.isOpen = true
            
            // Handle body scroll
            nextTick(() => {
                document.body.style.overflow = 'hidden'
            })
        }

        const close = () => {
            // If there are stacked modals, close the top one first
            if (modals.value.size > 0) {
                const topModalId = Array.from(modals.value.keys()).pop()
                return closeModal(topModalId)
            }
            
            // Otherwise close global modal
            const callback = globalModal.options.onCloseCallback
            
            globalModal.isOpen = false
            globalModal.component = null
            Object.keys(globalModal.props).forEach(key => delete globalModal.props[key])
            Object.keys(globalModal.options).forEach(key => delete globalModal.options[key])
            
            // Restore body scroll if no other modals
            document.body.style.overflow = ''
            
            // Execute callback after modal is closed
            if (callback && typeof callback === 'function') {
                nextTick(() => {
                    callback()
                })
            }
        }

        // Multi-modal support
        const openModal = (id, component, props = {}, options = {}, onCloseCallback = null) => {
            const modal = reactive({
                id,
                component: markRaw(component),
                props: { ...props },
                options: {
                    small: false,
                    title: null,
                    maxHeight: 600,
                    onCloseCallback,
                    ...options
                },
                isOpen: true
            })
            
            modals.value.set(id, modal)
            currentModalId.value = id
            
            // Handle body scroll
            nextTick(() => {
                document.body.style.overflow = 'hidden'
            })
            
            return modal
        }

        const closeModal = (id) => {
            const modal = modals.value.get(id)
            if (modal) {
                const callback = modal.options.onCloseCallback
                
                // Remove from modals map
                modals.value.delete(id)
                
                // Update current modal to the last one in the stack
                const remainingIds = Array.from(modals.value.keys())
                currentModalId.value = remainingIds.length > 0 ? remainingIds[remainingIds.length - 1] : null
                
                // Restore body scroll if no modals left
                if (modals.value.size === 0 && !globalModal.isOpen) {
                    document.body.style.overflow = ''
                }
                
                // Execute callback after modal is closed
                if (callback && typeof callback === 'function') {
                    nextTick(() => {
                        callback()
                    })
                }
            }
        }

        const closeAllModals = () => {
            modals.value.clear()
            currentModalId.value = null
            close()
            document.body.style.overflow = ''
        }

        const updateModalProps = (id, newProps) => {
            const modal = modals.value.get(id)
            if (modal) {
                Object.assign(modal.props, newProps)
            }
        }

        const getModal = (id) => {
            return modals.value.get(id)
        }

        // Legacy support for existing event bus pattern
        const openLegacyModal = (component, props = {}, options = {}) => {
            return open(component, props, options)
        }

        const closeLegacyModal = () => {
            return close()
        }

        return {
            // State
            modals: computed(() => modals.value),
            currentModalId: computed(() => currentModalId.value),
            
            // Getters
            isOpen,
            currentModal,
            showGlobalModal,
            hasMultipleModals,
            modalCount,
            
            // Global modal (legacy support)
            component: computed(() => globalModal.component),
            props: computed(() => globalModal.props),
            options: computed(() => globalModal.options),
            
            // Actions
            open,
            close,
            
            // Multi-modal actions
            openModal,
            closeModal,
            closeAllModals,
            updateModalProps,
            getModal,
            
            // Legacy actions
            openLegacyModal,
            closeLegacyModal
        }
    })
}