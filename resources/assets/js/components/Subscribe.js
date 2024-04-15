import React, { Component } from 'react';
import ReactDOM from 'react-dom';

class Subscribe extends Component {
  render(){
    return (
      <div className="subscribe-widget fl-wrap">
        <p>Bạn sẽ dễ dàng nhận được các chương trình khuyến mại từ WinBada.</p>
        <div className="subcribe-form">
          <form id="subscribe" noValidate="true">
            <input className="enteremail" name="EMAIL" id="subscribe-email" placeholder="Email" spellCheck="false" type="text" />
            <button type="submit" id="subscribe-button" className="subscribe-button"><i className="fa fa-rss" /> Đăng ký</button>
            <label htmlFor="subscribe-email" className="subscribe-message" />
          </form>
        </div>
      </div>
    )
  }
}
export default Subscribe;

if (document.getElementById('Subscribe')) {
    ReactDOM.render(<Subscribe />, document.getElementById('Subscribe'));
}