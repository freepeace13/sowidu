
export default class LightboxItem {
    media: Media;
    current: boolean = false;

    constructor(media, current = false) {
        this.media = media;
        this.current = current;
    }

    set(props) {
        Object.keys(props).forEach((prop) => this[prop] = props[prop]);

        return this;
    }

    get identifier() {
        return this.media.id;
    }

    set url(value) {
        this.media.url = value;
    }

    get url() {
        return this.media.url;
    }

    get isVideo() {
        return this.media.isVideo();
    }

    get isImage() {
        return this.media.isImage();
    }

    get mimeType() {
        return this.media.mimetype;
    }

    turnOff() {
        this.current = false;

        return this;
    }

    turnOn() {
        this.current = true;

        return this;
    }

    is(item) {
        return this.media.equals(item);
    }
}