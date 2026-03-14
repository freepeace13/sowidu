
export const convertToComponent = (template) => {
    if (typeof template === 'string') {
        return { template: `<span>${template}</span>` }
    }

    return template
}
