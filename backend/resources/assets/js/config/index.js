import { get } from '~/support/helpers';

const modules = ((context) => {
    return context.keys()
        .map((file) => ([
            file.replace(/(^.\/)|(\.js$)|(\/index)/g, ''),
            context(file)
        ]))
        .reduce((modules, [name, module]) => {
            if (module.default) {
                modules[name] = module.default
            }
            return modules;
        }, {});
})(require.context('.', false, /^((?!index).)*.js$/));

export default (paths: string, defval: any = null) => get(modules, paths, defval);

