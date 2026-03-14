<template>
    <div
        role="status"
        class="tw-animate-pulse tw-md:p-6 tw-px-2 tw-mb-4"
    >
        <div class="tw-flex tw-items-center tw-space-x-7">
            <v-avatar
                size="34"
                tile
            >
                <v-icon
                    x-large
                    light
                >
                    account_box
                </v-icon>
            </v-avatar>
            <div class="tw-w-full">
                <div
                    class="tw-w-full tw-h-2 tw-bg-gray-200 tw-rounded-full tw-dark:bg-gray-700 tw-mb-2"
                />
                <div
                    class="tw-h-2 tw-bg-gray-200 tw-rounded-full tw-dark:bg-gray-700 tw-w-32"
                />
            </div>
        </div>
        <span class="tw-sr-only">{{ $t('labels.loading') }}...</span>
    </div>
</template>
