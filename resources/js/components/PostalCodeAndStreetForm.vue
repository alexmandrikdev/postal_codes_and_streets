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
        };

        if (this.type === 'postalCode') {
            validations.name.minLength = minLength(5);
            validations.name.maxLength = maxLength(5);
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
                ? 'The postal code must be 5 digit'
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
