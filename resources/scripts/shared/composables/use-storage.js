import { StorageSerializers } from '@vueuse/core';

const useStorageDefaultOptions = {
    mergeDefaults: true,
    serializer: StorageSerializers.object,
};

export { useStorageDefaultOptions };
