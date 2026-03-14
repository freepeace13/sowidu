/** @flow */

type Avatar = { url: ?string }

export default (Base: Function) => class Avatarable extends Base {
    avatar: ?Avatar = { url: null };

    constructor(props: { avatar?: ?Avatar }): void {
        super(props);
        this.avatar = props.avatar || this.avatar;
    }
}