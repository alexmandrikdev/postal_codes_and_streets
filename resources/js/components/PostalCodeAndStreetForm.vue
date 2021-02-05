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
                    v-model="$v.name.$model"
                    :placeholder="nameInputPlaceholder"
                    :state="validateState('name')"
                />

                <b-form-invalid-feedback
                    >{{ nameInvalidFeedback }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group v-if="type === 'postalCode'" class="mx-2">
                <b-form-input
                    v-model="$v.locality.$model"
                    placeholder="Locality"
                    :state="validateState('locality')"
                />

                <b-form-invalid-feedback
                    >{{ nameInvalidFeedback }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group class="mx-2">
                <b-form-input
                    v-model="$v.latitude.$model"
                    placeholder="Latitude"
                    :state="validateState('latitude')"
                />

                <b-form-invalid-feedback>
                    {{ latitudeOrLongitudeInvalidFeedback('latitude') }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group class="mx-2">
                <b-form-input
                    v-model="$v.longitude.$model"
                    placeholder="Longitude"
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
    requiredIf,
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
            name: '',
            locality: '',
            latitude: '',
            longitude: '',
        };
    },
    validations() {
        const validations = {
            type: {
                required,
            },
            name: {
                required,
            },
            locality: {
                required: requiredIf(function() {
                    return this.type === 'postalCode';
                }),
            },
            latitude: {
                required,
                decimal,
                minValue: minValue(-90),
                maxValue: maxValue(90),
                maxLength: maxLength(10),
            },
            longitude: {
                required,
                decimal,
                minValue: minValue(-180),
                maxValue: maxValue(180),
                maxLength: maxLength(10),
            },
        };

        if (this.type === 'postalCode') {
            validations.name.minLength = minLength(6);
            validations.name.maxLength = maxLength(6);
            validations.name.numeric = numeric;
        }

        return validations;
    },
    computed: {
        nameInputPlaceholder() {
            return this.type === 'postalCode'
                ? 'Postal Code'
                : this.type === 'street'
                ? 'Street'
                : 'Postal Code or Street';
        },
        nameInvalidFeedback() {
            return !this.$v.name.required
                ? 'This field is required'
                : !this.$v.name.numeric
                ? 'The postal code must be numeric'
                : !this.$v.name.minLength || !this.$v.name.maxLength
                ? 'The postal code must be 6 characters'
                : '';
        },
    },
    methods: {
        validateState(name) {
            const { $dirty, $error } = this.$v[name];
            return $dirty ? !$error : null;
        },
        add() {
            this.$v.$touch();
            if (this.$v.$anyError) {
                return;
            }
        },
        latitudeOrLongitudeInvalidFeedback(type) {
            return !this.$v[type].required
                ? `This field is required`
                : !this.$v[type].decimal
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
                ? `The ${type}'s precision must be at most 7 number`
                : '';
        },
    },
};
</script>

<style scoped></style>
