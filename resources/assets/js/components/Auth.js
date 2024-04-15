import React, { Component } from 'react';
import ReactDOM from 'react-dom';

class Auth extends Component {
  render(){
    return (
      <div className="main-register-wrap modal">
        <div className="main-overlay" />
        <div className="main-register-holder">
          <div className="main-register fl-wrap">
            <div className="close-reg"><i className="fa fa-times" /></div>
            <h3>Sign In <span>Wind<strong>Bada</strong></span></h3>
            <div className="soc-log fl-wrap">
              <p>For faster login or register use your social account.</p>
              <a href="#" className="facebook-log"><i className="fa fa-facebook-official" />Log in with Facebook</a>
              <a href="#" className="twitter-log"><i className="fa fa-twitter" /> Log in with Twitter</a>
            </div>
            <div className="log-separator fl-wrap"><span>or</span></div>
            <div id="tabs-container">
              <ul className="tabs-menu">
                <li className="current"><a href="#tab-1">Login</a></li>
                <li><a href="#tab-2">Register</a></li>
              </ul>
              <div className="tab">
                <div id="tab-1" className="tab-content">
                  <div className="custom-form">
                    <form method="post" name="registerform">
                      <label>Username or Email Address * </label>
                      <input name="email" type="text" defaultValue />
                      <label>Password * </label>
                      <input name="password" type="password" defaultValue />
                      <button type="submit" className="log-submit-btn"><span>Log In</span></button>
                      <div className="clearfix" />
                      <div className="filter-tags">
                        <input id="check-a" type="checkbox" name="check" />
                        <label htmlFor="check-a">Remember me</label>
                      </div>
                    </form>
                    <div className="lost_password">
                      <a href="#">Lost Your Password?</a>
                    </div>
                  </div>
                </div>
                <div className="tab">
                  <div id="tab-2" className="tab-content">
                    <div className="custom-form">
                      <form method="post" name="registerform" className="main-register-form" id="main-register-form2">
                        <label>First Name * </label>
                        <input name="name" type="text" defaultValue />
                        <label>Second Name *</label>
                        <input name="name2" type="text" defaultValue />
                        <label>Email Address *</label>
                        <input name="email" type="text" defaultValue />
                        <label>Password *</label>
                        <input name="password" type="password" defaultValue />
                        <button type="submit" className="log-submit-btn"><span>Register</span></button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
export default Auth;

if (document.getElementById('AppAuth')) {
    ReactDOM.render(<Auth />, document.getElementById('AppAuth'));
}