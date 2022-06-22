/**
 * External dependencies
 */
import {
	BlockList,
	ObserveTyping,
	WritingFlow,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useSelect, useDispatch } from '@wordpress/data';
import { useCallback } from 'react';

export const EditorWritingFlow = () => {
	const { resetSelection } = useDispatch( blockEditorStore );

	const { firstBlock, isEmpty } = useSelect( ( select ) => {
		const blocks = select( 'core/block-editor' ).getBlocks();

		return {
			isEmpty: blocks.length
				? blocks.length <= 1 &&
				  blocks[ 0 ].attributes?.content?.trim() === ''
				: true,
			firstBlock: blocks[ 0 ],
		};
	} );

	// A combination of cursor on hover with CSS and this click handler ensures that clicking on
	// an empty editor starts you in the first paragraph and ready to type.
	const setSelectionOnClick = useCallback( () => {
		if ( isEmpty ) {
			const position = {
				offset: 0,
				clientId: firstBlock.clientId,
				attributeKey: 'content',
			};

			resetSelection( position, position, 0 );
		}
	}, [ isEmpty, firstBlock ] );

	return (
		<div
			style={ {
				display: 'flex',
				width: '100%',
				height: '100%',
				cursor: isEmpty ? 'text' : 'initial',
				padding: 6,
				'.block-editor-writing-flow': {
					width: '100%',
				},
			} }
			onClick={ setSelectionOnClick }
		>
			<WritingFlow>
				<ObserveTyping>
					<BlockList renderAppender={ () => <></> } />
				</ObserveTyping>
			</WritingFlow>
		</div>
	);
};
