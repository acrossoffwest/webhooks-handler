<template>
    <div>
        <b-form @reset="onReset" v-if="show" action="/webhooks" method="POST">
            <input type="hidden" name="_token" :value="token">
            <b-form-group
                id="input-group-1"
                label="Name:"
                label-for="input-1"
                description="Please provide webhook name"
            >
                <b-form-input
                    id="input-1"
                    v-model="form.name"
                    name="name"
                    required
                    placeholder="Enter webhook name"
                ></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-2" label="Your incoming URI endpoint:" label-for="input-2">
                <b-form-input
                    id="input-2"
                    v-model="form.in"
                    name="in"
                    required
                    placeholder="Enter URI for getting your webhooks"
                ></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-3" label="Your outcoming full URL endpoint:" label-for="input-2">
                <b-form-input
                    id="input-2"
                    v-model="form.out"
                    name="out"
                    required
                    placeholder="Enter full URL for proxying your webhooks"
                ></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-4" label="Default headers" label-for="input-3" description="Default header will merged/assigned to your outcoming requests">
                <v-jsoneditor v-model="form.default_headers" :options="{mode: 'code'}" :plus="false" height="400px"/>
                <input type="hidden" name="default_headers" v-model="default_headers">
            </b-form-group>

            <b-form-group id="input-group-6" label="Default payload" label-for="input-3" description="Default payload will merged/assigned to your outcoming requests">
                <v-jsoneditor v-model="form.default_payload" :options="{mode: 'code'}" :plus="false" height="400px"/>
                <input type="hidden" name="default_payload" v-model="default_payload">
            </b-form-group>

            <b-form-group id="input-group-5">
                <b-form-checkbox-group v-model="form.checked" id="checkboxes-4">
                    <b-form-checkbox value="me">Check me out</b-form-checkbox>
                    <b-form-checkbox value="that">Check that out</b-form-checkbox>
                </b-form-checkbox-group>
            </b-form-group>

            <b-button type="submit" variant="primary">Submit</b-button>
            <b-button type="reset" variant="danger">Reset</b-button>
        </b-form>
        <b-card class="mt-3" header="Form Data Result">
            <pre class="m-0">{{ form }}</pre>
        </b-card>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    name: '',
                    in: null,
                    out: null,
                    default_headers: {},
                    default_payload: {}
                },
                foods: [{ text: 'Select One', value: null }, 'Carrots', 'Beans', 'Tomatoes', 'Corn'],
                show: true
            }
        },
        computed: {
            token() {
                return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            default_headers () {
                return JSON.stringify(this.form.default_headers)
            },
            default_payload () {
                return JSON.stringify(this.form.default_payload)
            }
        },
        methods: {
            onSubmit(evt) {
                evt.preventDefault()
                alert(JSON.stringify(this.form))
            },
            onReset(evt) {
                evt.preventDefault()
                // Reset our form values
                this.form.email = ''
                this.form.name = ''
                this.form.food = null
                this.form.checked = []
                // Trick to reset/clear native browser form validation state
                this.show = false
                this.$nextTick(() => {
                    this.show = true
                })
            }
        }
    }
</script>
