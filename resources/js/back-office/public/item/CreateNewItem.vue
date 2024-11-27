<template>
  <div>
    <select-category :classifications="package.categories" v-model="form.category"/>
    <question-code-form v-model="form.code"></question-code-form>
    <bundle v-if="type === ITEM_TYPE_BUNDLE" :title="title" :config="config" v-model="form"/>
    <multi-choice-single v-else-if="type === ITEM_TYPE_MULTI_CHOICE_SINGLE" :title="title" :config="config" v-model="form"/>
    <button class="btn btn-success btn-block mt-2" @click="save"><i class="fa fa-save"/>&nbsp; Save</button>
  </div>
</template>

<script>
  import Bundle from "../../components/quiz-builder/Bundle";
  import MultiChoiceSingle from "../../components/quiz-builder/MultiChoiceSingle";
  import SelectCategory from "../../components/classifiable/Select";
  import QuestionCodeForm from "../../components/extra/QuestionCodeForm";

  export default {
    name: "CreateItemComponent",
    components: {QuestionCodeForm, SelectCategory, Bundle, MultiChoiceSingle },
    props: {
      config: {
        required: true,
        type: Object
      },
      id: {
        required: true,
        type: String
      },
      title: {
        default: null,
        type: String
      }
    },
    data: () => ({
      package: {
        categories: [],
      },
      form: {
        category: null,
        code: null
      }
    }),
    computed: {
      type: function () {
        return _.get(this.config, 'type', this.ITEM_TYPE_MULTI_CHOICE_SINGLE)
      }
    },
    watch: {
      'package.categories': function (newValue) {
        if (newValue.length > 0) {
          this.form.category = newValue[0].hash
        }
      }
    },
    methods: {
      save () {
        this.request('api.back-office.package.item.store', { 'package__': this.id }, {
          data: this.form
        }).then(res => {
          this.swal(res.data.status, res.data.message, res.data.status).then(() => this.$emit('success'))
        })
      }
    },
    mounted () {
      this.request('api.back-office.package.show', { 'package__': this.id }).then(res => {
        this.package = res.data
      })
    }
  }
</script>

<style scoped>

</style>
