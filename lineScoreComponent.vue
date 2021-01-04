var LineScoreComponent = {
  name: 'LineScoreComponent',
  template: `
    <div class='container'>
LineScoreComponent
    </div>
  `,
  props: ['situation'],
  data: function() {
    return {
      team: {'team_name':''},
      teamname: 't',
    }
  },
  watch: {
  },
  mounted() {
    eBus.$on('teamUpdated',(t) => { this.team = t;});
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
  },
};

