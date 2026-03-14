/** @flow */

type InvitationProps = {
    recipient: any,
    message: string
}

type PropTypes = { type: InvitationType } & InvitationProps;

export default class InvitationMessage {
    recipient: any;
    type: InvitationType;
    message: string;

    constructor(props: PropTypes): void {
        this.recipient = props.recipient;
        this.type = props.type;
        this.message = props.message;
    }

    static employment(props: InvitationProps): InvitationMessage {
        return new InvitationMessage({ ...props, type: 'employment' });
    }

    static contact(props: InvitationProps): InvitationMessage {
        return new InvitationMessage({ ...props, type: 'contact' });
    }
}