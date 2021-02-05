<template>
    <div>
        <h4 class="text-center text-dark">Add new Postal Code or Street</h4>
        <b-form
            class="d-lg-flex justify-content-center align-items-start mt-3"
            @submit.prevent="add"
        >
            <b-form-group class="mx-2">
                <b-form-select
                    v-model="$v.type.$model"
                    :options="typeOptions"
                    :state="validateState('type')"
                />

                <b-form-invalid-feedback
                    >This field is required</b-form-invalid-feedback
                >
            </b-form-group>

            <b-form-group class="mx-2">
                <b-form-input
                    v-model="$v.postalCode.$model"
                    placeholder="Postal Code"
                    :state="validateState('postalCode')"
                />

                <b-form-invalid-feedback
                    >{{ postalCodeInvalidFeedback }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group class="mx-2">
                <b-form-input
                    v-model="$v.localityOrStreet.$model"
                    :placeholder="
                        type === 'street'
                            ? 'Street'
                            : type === 'postalCode'
                            ? 'Locality'
                            : 'Locality Or Street'
                    "
                    :state="validateState('localityOrStreet')"
                />

                <b-form-invalid-feedback
                    >{{ localityOrStreetInvalidFeedback }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group class="mx-2">
                <b-form-input
                    v-model="$v.latitude.$model"
                    placeholder="Latitude (optional)"
                    :state="validateState('latitude')"
                />

                <b-form-invalid-feedback>
                    {{ latitudeOrLongitudeInvalidFeedback('latitude') }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group class="mx-2">
                <b-form-input
                    v-model="$v.longitude.$model"
                    placeholder="Longitude (optional)"
                    :state="validateState('longitude')"
                />

                <b-form-invalid-feedback>
                    {{ latitudeOrLongitudeInvalidFeedback('longitude') }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group class="mx-2 text-center">
                <b-button type="submit" variant="primary">Add</b-button>
            </b-form-group>
        </b-form>
    </div>
</template>

<script>
import { validationMixin } from 'vuelidate';
import {
    required,
    minLength,
    maxLength,
    minValue,
    maxValue,
    numeric,
    decimal,
} from 'vuelidate/lib/validators';

export default {
    mixins: [validationMixin],
    data() {
        return {
            type: null,
            typeOptions: [
                { value: null, text: 'Type', disabled: true },
                { value: 'postalCode', text: 'Postal Code' },
                { value: 'street', text: 'Street' },
            ],
            postalCode: '',
            localityOrStreet: '',
            latitude: '',
            longitude: '',
            postalCodehasAlreadyBeenTaken: false,
        };
    },
    validations: {
        type: {
            required,
        },
        postalCode: {
            required,
            minLength: minLength(5),
            maxLength: maxLength(5),
            numeric,
        },
        localityOrStreet: {
            required,
            maxLength: maxLength(255),
        },
        latitude: {
            decimal,
            minValue: minValue(-90),
            maxValue: maxValue(90),
            maxLength: maxLength(10),
        },
        longitude: {
            decimal,
            minValue: minValue(-180),
            maxValue: maxValue(180),
            maxLength: maxLength(11),
        },
    },
    computed: {
        postalCodeInvalidFeedback() {
            return !this.$v.postalCode.required
                ? 'This field is required'
                : !this.$v.postalCode.numeric
                ? 'The postal code must be numeric'
                : !this.$v.postalCode.minLength || !this.$v.postalCode.maxLength
                ? 'The postal code must be 5 digit'
                : this.postalCodehasAlreadyBeenTaken
                ? 'The postal code has already added'
                : '';
        },
        localityOrStreetInvalidFeedback() {
            return !this.$v.localityOrStreet.required
                ? 'This field is required'
                : !this.$v.localityOrStreet.maxLength
                ? 'This value must me at most 255 characters'
                : '';
        },
    },
    methods: {
        validateState(name) {
            if (name === 'postalCode' && this.postalCodehasAlreadyBeenTaken) {
                return false;
            }

            const { $dirty, $error } = this.$v[name];
            return $dirty ? !$error : null;
        },
        add() {
            this.$v.$touch();
            if (this.$v.$anyError) {
                return;
            }

            if (this.type === 'postalCode') {
                axios
                    .post('/api/v1/postal_codes', {
                        postal_code: this.postalCode,
                        locality: this.localityOrStreet,
                        latitude: this.latitude,
                        longitude: this.longitude,
                    })
                    .then(() => {
                        this.resetForm();
                    })
                    .catch(({ response }) => {
                        if (
                            response.status === 422 &&
                            response.data.errors.postal_code &&
                            response.data.errors.postal_code[0] ===
                                'The postal code has already been taken.'
                        ) {
                            this.postalCodehasAlreadyBeenTaken = true;
                        }
                    });
            }

            if (this.type === 'street') {
                axios
                    .post('/api/v1/streets', {
                        postal_code: this.postalCode,
                        name: this.localityOrStreet,
                        latitude: this.latitude,
                        longitude: this.longitude,
                    })
                    .then(() => {
                        this.resetForm();
                    });
            }
        },
        resetForm() {
            this.postalCode = '';
            this.localityOrStreet = '';
            this.latitude = '';
            this.longitude = '';

            this.postalCodehasAlreadyBeenTaken = false;

            this.$v.$reset();
        },
        latitudeOrLongitudeInvalidFeedback(type) {
            return !this.$v[type].decimal
                ? `The ${type} must be decimal`
                : !this.$v[type].minValue
                ? `The ${type} must be at least ${
                      type === 'latitude' ? -90 : -180
                  }`
                : !this.$v[type].maxValue
                ? `The ${type} must be at most ${
                      type === 'latitude' ? 90 : 180
                  }`
                : !this.$v[type].maxLength
                ? `The ${type}'s scale must be at most 7`
                : '';
        },
    },
};
</script>

<style scoped></style>
