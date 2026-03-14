import * as Comlink from 'comlink';
import Worker from '~/services/workers/async.worker';

export default (handler) => (resolve, reject) => {
    Comlink.wrap(new Worker())(
        Comlink.proxy(handler),
        Comlink.proxy(resolve),
        Comlink.proxy(reject)
    );
}