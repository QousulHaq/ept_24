<template>
  <div>
    <select-category :classifications="package.categories" v-model="form.category"/>
    <question-code-form :disabled="! editableCode" v-model="form.code"></question-code-form>
    <bundle v-if="ready && type === ITEM_TYPE_BUNDLE" :title="title" :config="config" v-model="form"/>
    <multi-choice-single v-else-if="ready && type === ITEM_TYPE_MULTI_CHOICE_SINGLE" :title="title" :config="config" v-model="form"/>
    <button class="btn btn-success btn-block mt-2" @click="save"><i class="fa fa-save"/>&nbsp; Save</button>
  </div>
</template>

<script>
  import Bundle from "../../components/quiz-builder/Bundle";
  import MultiChoiceSingle from "../../components/quiz-builder/MultiChoiceSingle";
  import SelectCategory from "../../components/classifiable/Select";
  import QuestionCodeForm from "../../components/extra/QuestionCodeForm";

  export default {
    name: "EditItemComponent",
    components: { SelectCategory, QuestionCodeForm, Bundle, MultiChoiceSingle },
    props: {
      editableCode: {
        default: true,
      },
      config: {
        required: true,
        type: Object
      },
      packageId: {
        required: true,
        type: String
      },
      itemId: {
        required: true,
        type: String
      },
      title: {
        default: null,
        type: String
      }
    },
    data: () => ({
      ready: false,
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
        this.request('api.back-office.package.item.update', {
          'package__': this.packageId,
          'item': this.itemId
        }, {
          data: this.form
        }).then(res => {
          this.swal(res.data.status, res.data.message, res.data.status).then(() => this.$emit('success'))
        })
      }
    },
    async created () {
      await this.request('api.back-office.package.show', { 'package__': this.packageId }).then(res => {
        this.package = res.data
      });

      await this.request('api.back-office.package.item.show', {
        'package__': this.packageId, 'item': this.itemId
      }).then(res => {
        this.form = res.data;
      });

      this.ready = true
    }
  }
</script>

<style scoped>

</style>
