import InputTextField from '@components/InputTextField.vue'
import InputTextArea from '@components/InputTextArea.vue'
import InputSelect from '@components/InputSelect.vue'
import InputAvatar from '@components/InputAvatar.vue'

export default {
    components: {
        InputTextField,
        InputTextArea,
        InputSelect,
        InputAvatar,
    },

    data: () => ({
        states: [],
    }),

    // watch: {
    //     'form.country': {
    //         immediate: true,
    //         handler (country) {
    //             if (! country) return;
    //             this.states = [];

    //             Http.get(this.$route('countries.states', { country }))
    //                 .then((response) => {
    //                     this.states = response.data.states;
    //                 });
    //         },
    //     },
    // },

    methods: {
        selectAvatar(data) {
            this.form.avatar = new File(
                [data.input.file],
                data.input.file.name,
                { type: data.input.file.type },
            )
        },
    },
}
