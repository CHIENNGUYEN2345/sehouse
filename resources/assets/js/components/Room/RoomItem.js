import React, { Component } from 'react';
import ReactDOM from 'react-dom';

class RoomItem extends Component {

    render() {
        return (
            <div className="listing-item">
                <article className="geodir-category-listing fl-wrap">
                    <div className="geodir-category-img">
                        <img src="assets/images/all/1.jpg" alt />
                        <div className="overlay" />
                        <div className="list-post-counter"><span>4</span><i className="fa fa-heart" /></div>
                    </div>
                    <div className="geodir-category-content fl-wrap">
                        <a className="listing-geodir-category" href="#">{this.props.space_type_name}</a>
                        <div className="listing-avatar"><a href="#"><img src="assets/images/avatar/5.jpg" alt /></a>
                            <span className="avatar-tooltip">Added By  <strong>Lisa Smith</strong></span>
                        </div>
                        <h3><a href="listing-single.html">{ this.props.children }</a></h3>
                        <p>Sed interdum metus at nisi tempor laoreet.</p>
                        <div className="geodir-category-options fl-wrap">
                            <div className="listing-rating card-popup-rainingvis" data-starrating2={this.props.overall_rating}>
                                <span>(7 đánh giá)</span>
                            </div>
                            <div className="geodir-category-location"><a href="#"><i className="fa fa-map-marker" aria-hidden="true" /> 27th Brooklyn New York, NY 10065</a></div>
                        </div>
                    </div>
                </article>
            </div>
        )
    }
}
export default RoomItem;