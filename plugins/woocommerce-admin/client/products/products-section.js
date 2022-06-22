/**
 * External dependencies
 */

/**
 * Internal dependencies
 */

export const ProductsSection = ( { title, description, children } ) => {
	return (
		<div className="woo-products-section">
			<div className="woo-products-section__desc">
				<h2>{ title }</h2>
				<p> { description }</p>
			</div>
			<div className="woo-products-section__form">{ children }</div>
		</div>
	);
};
