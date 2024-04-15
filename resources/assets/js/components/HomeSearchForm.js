import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

class HomeSearchForm extends Component {

  constructor(props) {
    super(props);
    this.state = {
      users: []
    };
  }

  componentDidMount() {
    axios.get("https://fcctop100.herokuapp.com/api/fccusers/top/recent")
      .then(response => {
        this.setState({ users: response.data });
      })
      .catch(err => console.log(err));
  } 

  render(){
    return (
      <div className="main-search-input-wrap">
        <div className="main-search-input fl-wrap">
          <div className="main-search-input-item location" id="autocomplete-container">
            <input type="text" placeholder="Bạn muốn đến đâu?" id="autocomplete-input" defaultValue />
            <a href="#"><i className="fa fa-dot-circle-o" /></a>
          </div>
          <div className="main-search-input-item book-date">                                            
            <input type="text" placeholder="Ngày đi" className="datepicker" data-default-date data-large-mode="true" data-large-default="true" defaultValue />
          </div>
          <div className="main-search-input-item book-date">
            <input type="text" placeholder="Ngày về" className="datepicker" data-default-date data-large-mode="true" data-large-default="true" defaultValue />
          </div>
          <div className="main-search-input-item people">
            <select data-placeholder="Số người" className="chosen-select">
              <option>Số người</option>
              <option>1</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
              <option>6</option>
            </select>
          </div>
          <button className="main-search-button">Tìm kiếm</button>
        </div>
      </div>
    )
  }
}
export default HomeSearchForm;

if (document.getElementById('HomeSearchForm')) {
    ReactDOM.render(<HomeSearchForm />, document.getElementById('HomeSearchForm'));
}