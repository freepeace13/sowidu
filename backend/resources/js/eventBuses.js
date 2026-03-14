const modules = import.meta.glob(
    '../../modules/**/resources/js/Services/EventBus.js',
    { eager: true },
)

export const moduleBuses = Object.values(modules)
    .map((module) => module.EventBus)
    .filter((bus) => bus)
