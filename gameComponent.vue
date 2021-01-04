var GameComponent = {
  name: 'GameComponent',
  template: `
    <div class='container'>
{{ currentComponent() }}
      <b-row>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='#' v-on:click='switchToMenu();'>
            Pss Menu
          </b-button>
        </b-col>
        <b-col cols='8'>
        </b-col>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='/jhmlhome.html'>
            JHML Home
          </b-button>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols='6'>
          <situation-component v-bind:gameInfo="gameInfo"></situation-component>
        </b-col>
        <b-col cols='6'>
          <line-score-component v-bind:gameInfo="gameInfo"></line-score-component>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols='12'>
          <game-entry-component v-bind:gameInfo="gameInfo"></game-entry-component>
        </b-col>
      </b-row>
    </div>
  `,
  data() {
    return {
      team: {"team_name":""},
      teamname: 't',
    }
  },
  components: {
    'game-entry-component': GameEntryComponent,
    'line-score-component': LineScoreComponent,
    'situation-component': SituationComponent,
  },
  watch: {
  },
  mounted() {
    eBus.$on('teamUpdated',(t) => { this.team = t;});
  },
  computed: {
    gameInfo() { if (vue == undefined) return {'visitor':{'name':'','gameNumber':''},'home':{'name':'','gameNumber':''}}; return vue.gameInfo; },
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    switchToMenu() {
      vue.currentComponent='menuComponent';
      vue.emitData();
    },
  },
};

