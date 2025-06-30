import { reactive, computed, watchEffect } from "vue";
import EventBus, { EVENT } from "#/event-bus.js";

export function useWindowState() {
    // Reactive global state
    const globalState = reactive({
        windowWidth: window.innerWidth,
        windowHeight: window.innerHeight,
        get $isMobile() {
            return this.windowWidth <= 760;
        },
        get $screenOrientation() {
            return this.windowWidth > this.windowHeight ? "landscape" : "portrait";
        },
    });

    const onResize = () => {
        globalState.windowWidth = window.innerWidth;
        globalState.windowHeight = window.innerHeight;
        EventBus.emit(EVENT.IS_MOBILE_CHANGED, globalState.$isMobile);
    };

    watchEffect((cleanup) => {
        window.addEventListener("resize", onResize);
        cleanup(() => {
            window.removeEventListener("resize", onResize); // ✅ Cleanup when no longer used
        });
    });

    return {
        globalState,
        windowWidth: computed(() => globalState.windowWidth),
        windowHeight: computed(() => globalState.windowHeight),
        $isMobile: computed(() => globalState.$isMobile),
        $screenOrientation: computed(() => globalState.$screenOrientation),
    };
}
