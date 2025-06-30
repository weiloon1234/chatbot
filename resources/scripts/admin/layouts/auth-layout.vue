<template>
    <div class="relative z-1 w-full p-6 sm:p-0">
        <div class="relative flex h-screen w-full flex-col justify-center sm:p-0 lg:flex-row">
            <div class="flex w-full flex-1 flex-col lg:w-1/2">
                <router-view />
            </div>
            <div class="bg-brand-950 relative hidden h-full w-full items-center lg:grid lg:w-1/2">
                <div class="z-1 flex items-center justify-center">
                    <!-- ===== Common Grid Shape Start ===== -->
                    <div class="absolute top-0 right-0 -z-1 w-full max-w-[250px] xl:max-w-[450px]">
                        <img src="/img/admin/shape/grid-01.svg" alt="grid" />
                    </div>
                    <div class="absolute bottom-0 left-0 -z-1 w-full max-w-[250px] rotate-180 xl:max-w-[450px]">
                        <img src="/img/admin/shape/grid-01.svg" alt="grid" />
                    </div>

                    <div class="flex max-w-xs flex-col items-center">
                        <a href="javascript:" class="mb-4 block">
                            <img src="/logo.png" alt="Logo" />
                        </a>
                        <p class="text-center font-semibold text-gray-400">
                            {{ $appName }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
