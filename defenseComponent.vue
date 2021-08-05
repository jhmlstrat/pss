var DefenseComponent = {
  name: 'DefenseComponent',
  template: `
    <div class='container'>
DefenseComponent
      <b-row>
        <b-col cols=4 class="text-center">
        </b-col>
        <b-col cols=4 class="text-center">
          <span v-html="fielder(8)"></span>
        </b-col>
        <b-col cols=4 class="text-center">
        </b-col>
      </b-row>
      <b-row>
        <b-col cols=4 class="text-center">
          <span v-html="fielder(7)"></span>
        </b-col>
        <b-col cols=4 class="text-center">
        </b-col>
        <b-col cols=4 class="text-center">
          <span v-html="fielder(9)"></span>
        </b-col>
      </b-row>
      <b-row>&nbsp;</row>
      <b-row>
        <b-col cols=2 class="text-center">
        </b-col>
        <b-col cols=3 class="text-center">
          <span v-html="fielder(6)"></span>
        </b-col>
        <b-col cols=2 class="text-center">
        </b-col>
        <b-col cols=3 class="text-center">
          <span v-html="fielder(4)"></span>
        </b-col>
        <b-col cols=2 class="text-center">
        </b-col>
      </b-row>
{{ roster() }}
    </div>
  `,
  props: ['gameInfo','vRoster','hRoster'],
  data: function() {
    return {
    }
  },
  watch: {
  },
  mounted() {
  },
  computed: {
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    roster() {
      if (this.gameInfo.situation.situation.side == 0) {
        return this.hRoster;
      }
      return this.vRoster;
    },
    fielder(spot) {
      rtn = '<span ';
      rtn += ' title="foo"';
      rtn += '>' + spot;
      rtn += '</span>';
      return rtn;
    },
  },
};

